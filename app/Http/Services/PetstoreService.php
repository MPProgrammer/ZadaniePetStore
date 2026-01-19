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

    public function getPet($id)
    {
        return $this->client()->get("$this->baseUrl/pet/$id");
    }

    public function findByStatus(string $status)
    {
        return $this->client()->get(
            $this->baseUrl . '/pet/findByStatus',
            ['status' => $status]
        );
    }

    public function createPet(array $data)
    {
        return $this->client()->post(
            $this->baseUrl . '/pet',
            [
                // 'id' => $data['id'],
                'name' => $data['name'],
                'status' => $data['status'],
                'photoUrls' => [],
            ]
        );
    }

    public function updatePet(array $data)
    {
        return $this->client()->put(
            $this->baseUrl . '/pet',
            [
                'id' => $data['id'],
                'name' => $data['name'],
                'status' => $data['status'],
                'photoUrls' => [],
            ]
        );
    }

    public function deletePet(int $id)
    {
        return $this->client()->delete(
            $this->baseUrl . '/pet/' . $id
        );
    }
}
