<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Services\RadnetDnsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Exception;

class CreateDomainJob implements ShouldQueue
{
    use Queueable;

    public $domain;
    public $tries = 3;
    public $backoff = [10, 60, 300]; // 10 seconds, 1 minute, 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $radnetService = new RadnetDnsService();

            // Update status to provisioning
            $this->domain->update(['status' => 'provisioning']);

            // Call RADNET API to create subdomain
            $response = $radnetService->createSubdomain(
                $this->domain->subdomain,
                $this->domain->target_ip
            );

            // Store response and mark as active
            $this->domain->update([
                'status' => 'active',
                'radnet_response' => $response,
            ]);

            // Log activity
            activity_log($this->domain->user_id, 'domain_provisioned', "Domain provisioned: {$this->domain->full_domain}", null);

            // Send notification to user
            send_notification($this->domain->user_id, 'Domain Active', "Your domain {$this->domain->full_domain} is now active!");

        } catch (Exception $e) {
            // Log the error
            $this->domain->update([
                'status' => 'failed',
                'radnet_response' => [
                    'error' => $e->getMessage(),
                    'attempt' => $this->attempts(),
                ],
            ]);

            // Log activity
            activity_log($this->domain->user_id, 'domain_failed', "Domain provisioning failed: {$this->domain->full_domain} - {$e->getMessage()}", null);

            // Send notification to user on final failure
            if ($this->attempts() >= $this->tries) {
                send_notification($this->domain->user_id, 'Domain Failed', "Domain provisioning failed: {$this->domain->full_domain}. Please contact admin.");
            }

            // Re-throw exception to trigger retry
            throw $e;
        }
    }
}

