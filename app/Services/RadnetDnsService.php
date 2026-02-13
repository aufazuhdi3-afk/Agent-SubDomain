<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class RadnetDnsService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.radnet.api_url') ?? env('RADNET_API_URL');
        $this->apiKey = config('services.radnet.api_key') ?? env('RADNET_API_KEY');
        // If running in local environment or RADNET credentials are missing/placeholder,
        // run in mock mode for local/testing to avoid real HTTP calls.
        $isLocal = env('APP_ENV') === 'local' || env('APP_ENV') === 'development';
        $isPlaceholderKey = empty($this->apiKey) || str_contains($this->apiKey, 'your_');
        if ($isLocal || !$this->apiUrl || $isPlaceholderKey) {
            $this->mock = true;
            return;
        }
        $this->mock = false;
    }

    /**
     * Create a subdomain via RADNET API.
     *
     * @param string $subdomain
     * @param string $targetIp
     * @return array
     * @throws Exception
     */
    public function createSubdomain(string $subdomain, string $targetIp): array
    {
        // If mock mode, return a fake successful response.
        if (!empty($this->mock)) {
            return [
                'mock' => true,
                'subdomain' => $subdomain,
                'target_ip' => $targetIp,
                'message' => 'Mocked RADNET create success',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($this->apiUrl . '/api/dns/create', [
                'subdomain' => $subdomain,
                'target_ip' => $targetIp,
                'domain' => 'unnar.id',
            ]);

            if ($response->failed()) {
                throw new Exception('RADNET API error: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to create subdomain: ' . $e->getMessage());
        }
    }

    /**
     * Delete a subdomain via RADNET API.
     *
     * @param string $subdomain
     * @return array
     * @throws Exception
     */
    public function deleteSubdomain(string $subdomain): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($this->apiUrl . '/api/dns/delete', [
                'subdomain' => $subdomain,
                'domain' => 'unnar.id',
            ]);

            if ($response->failed()) {
                throw new Exception('RADNET API error: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to delete subdomain: ' . $e->getMessage());
        }
    }

    /**
     * Update a subdomain target IP via RADNET API.
     *
     * @param string $subdomain
     * @param string $targetIp
     * @return array
     * @throws Exception
     */
    public function updateSubdomain(string $subdomain, string $targetIp): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($this->apiUrl . '/api/dns/update', [
                'subdomain' => $subdomain,
                'target_ip' => $targetIp,
                'domain' => 'unnar.id',
            ]);

            if ($response->failed()) {
                throw new Exception('RADNET API error: ' . $response->body());
            }

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to update subdomain: ' . $e->getMessage());
        }
    }
}
