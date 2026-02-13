<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Jobs\CreateDomainJob;
use Illuminate\Http\Request;

class AdminDomainController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display a listing of all domains.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = Domain::with('user')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $domains = $query->paginate(20);

        return view('admin.domains.index', compact('domains', 'status'));
    }

    /**
     * Approve a domain request and dispatch provisioning job.
     */
    public function approve(Domain $domain)
    {
        if ($domain->status !== 'pending') {
            return back()->with('error', 'Only pending domains can be approved.');
        }

        $domain->update(['status' => 'approved']);

        // Log activity
        activity_log(auth()->id(), 'domain_approved', "Approved domain: {$domain->full_domain}", request()->ip());

        // Dispatch job to provision domain
        CreateDomainJob::dispatch($domain);

        return back()->with('success', 'Domain approved and provisioning started.');
    }

    /**
     * Reject a domain request.
     */
    public function reject(Domain $domain)
    {
        if ($domain->status !== 'pending') {
            return back()->with('error', 'Only pending domains can be rejected.');
        }

        $domain->update(['status' => 'failed']);

        // Log activity
        activity_log(auth()->id(), 'domain_rejected', "Rejected domain: {$domain->full_domain}", request()->ip());

        // Send notification
        send_notification($domain->user_id, 'Domain Rejected', "Your domain request for {$domain->full_domain} was rejected.");

        return back()->with('success', 'Domain rejected.');
    }

    /**
     * Suspend an active domain.
     */
    public function suspend(Domain $domain)
    {
        if ($domain->status !== 'active') {
            return back()->with('error', 'Only active domains can be suspended.');
        }

        $domain->update(['status' => 'suspended']);

        // Log activity
        activity_log(auth()->id(), 'domain_suspended', "Suspended domain: {$domain->full_domain}", request()->ip());

        return back()->with('success', 'Domain suspended.');
    }

    /**
     * Retry provisioning for a failed domain.
     */
    public function retryProvision(Domain $domain)
    {
        if ($domain->status !== 'failed') {
            return back()->with('error', 'Only failed domains can be retried.');
        }

        $domain->update([
            'status' => 'provisioning',
            'radnet_response' => null,
        ]);

        // Log activity
        activity_log(auth()->id(), 'domain_retry', "Retrying domain: {$domain->full_domain}", request()->ip());

        // Dispatch job to try again
        CreateDomainJob::dispatch($domain);

        return back()->with('success', 'Domain provisioning retry started.');
    }
}

