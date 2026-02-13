<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of user's domains.
     */
    public function index()
    {
        $domains = auth()->user()->domains()->latest()->paginate(10);
        return view('domains.index', compact('domains'));
    }

    /**
     * Show the form for creating a new domain.
     */
    public function create()
    {
        return view('domains.create');
    }

    /**
     * Store a newly created domain in storage.
     */
    public function store(Request $request)
    {
        // Rate limit check: 3 requests per day
        $today = Domain::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->count();

        if ($today >= 3) {
            return back()->with('error', 'You have reached the daily limit of 3 domain requests. Try again tomorrow.');
        }

        // Validate domain limit
        if (!Domain::canCreateNew(auth()->id())) {
            return back()->with('error', 'You have reached the maximum limit of 3 domains.');
        }

        $validated = $request->validate([
            'subdomain' => [
                'required',
                'regex:/^[a-z0-9-]+$/',
                'unique:domains,subdomain',
                'max:63',
            ],
            'target_ip' => [
                'required',
                'ip',
            ],
        ], [
            'subdomain.regex' => 'Subdomain can only contain lowercase letters, numbers, and hyphens.',
        ]);

        $fullDomain = $validated['subdomain'] . '.unnar.id';

        $domain = auth()->user()->domains()->create([
            'subdomain' => $validated['subdomain'],
            'full_domain' => $fullDomain,
            'target_ip' => $validated['target_ip'],
            'status' => 'pending',
        ]);

        // Log activity
        activity_log(auth()->id(), 'domain_requested', "Requested subdomain: {$fullDomain}", $request->ip());

        return redirect()->route('domains.index')
            ->with('success', 'Domain request submitted successfully! Awaiting admin approval.');
    }

    /**
     * Remove the specified domain from storage.
     */
    public function destroy(Domain $domain)
    {
        if ($domain->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        if (in_array($domain->status, ['active', 'provisioning'])) {
            return back()->with('error', 'Cannot delete active or provisioning domains.');
        }

        // Log activity
        activity_log(auth()->id(), 'domain_deleted', "Deleted domain request: {$domain->full_domain}", request()->ip());

        $domain->delete();

        return back()->with('success', 'Domain request deleted successfully.');
    }
}

