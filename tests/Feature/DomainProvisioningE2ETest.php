<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainProvisioningE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_full_domain_provisioning_flow()
    {
        // Use sync queue so jobs run immediately in test
        config(['queue.default' => 'sync']);

        // Create users
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        // Owner requests domain
        $this->actingAs($owner)
            ->post(route('domains.store'), [
                'subdomain' => 'e2e-flow',
                'target_ip' => '203.0.113.20',
            ])
            ->assertRedirect(route('domains.index'));

        $domain = Domain::where('subdomain', 'e2e-flow')->first();
        $this->assertNotNull($domain);
        $this->assertEquals('pending', $domain->status);

        // Admin approves - job should run immediately (sync)
        $this->actingAs($admin)
            ->post(route('admin.domains.approve', ['domain' => $domain->id]))
            ->assertSessionHas('success');

        $domain->refresh();
        $this->assertEquals('active', $domain->status);
        $this->assertIsArray($domain->radnet_response ?? []);

        // Check activity logs
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'action' => 'domain_provisioned',
        ]);
    }
}
