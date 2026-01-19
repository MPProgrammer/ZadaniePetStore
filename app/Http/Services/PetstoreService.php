<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class PetstoreService
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.petstore.url');
        $this->apiKey = config('services.petstore.key');
    }

    private function client()
    {
        return Http::withHeaders([
            'api_key' => $this->apiKey,
            'Accept' => 'application/json'
        ]);
    }

    public function findByStatus(string $status)
    {
        return $this->client()->get(
            $this->baseUrl . '/pet/findByStatus',
            ['status' => $status]
        );
    }
}
