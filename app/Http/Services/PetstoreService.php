<?php

/**
 * @author    Panek Mariusz <mariusz@codelarix.dev>
 * @copyright Copyright © Codelarix (https://codelarix.dev)
 * @version   1.0.0
 */

declare(strict_types=1);

namespace App\Http\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PetstoreService
{
    /** @var string The base URL */
    private string $baseUrl;

    /** @var string The API key */
    private $apiKey;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        // Set the base URL for the API
        $this->baseUrl = config('services.petstore.url');

        // Set the API key
        $this->apiKey = config('services.petstore.key');
    }

    /**
     * Get a new instance of the HTTP client with the API key set as a header.
     *
     * @return PendingRequest The HTTP client instance
     */
    private function client(): PendingRequest
    {
        // Set the API key, Accept header to application/json.
        $headers = [
            'api_key' => $this->apiKey,
            'Accept'  => 'application/json'
        ];

        // Return the HTTP client with the headers set.
        return Http::withHeaders($headers);
    }

    /**
     * Get a pet by its ID.
     *
     * @param int|string $id The pet ID
     *
     * @return Response The HTTP response
     */
    public function getPet(int|string $id): Response
    {
        // Send a GET request to the /pet/{id} endpoint.
        return $this->client()->get($this->baseUrl . "/pet/" . $id);
    }

    /**
     * Find pets by their status.
     *
     * @param string $status The pet status
     *
     * @return Response The HTTP response
     */
    public function findByStatus(string $status): Response
    {
        // Send a GET request to the /pet/findByStatus endpoint with the status parameter set.
        return $this->client()->get(
            $this->baseUrl . '/pet/findByStatus',
            [
                'status' => $status
            ]
        );
    }

    /**
     * Create a new pet in the petstore.
     *
     * @param string $name The name of the pet to be created
     * @param string $status The status of the pet to be created
     *
     * @return Response The HTTP response
     */
    public function createPet(string $name, string $status): Response
    {
        // Send a POST request to the /pet endpoint with the name and status set.
        // The photoUrls parameter is required by the API, but it is not used in this case.
        return $this->client()->post(
            $this->baseUrl . '/pet',
            [
                'name'      => $name,
                'status'    => $status,
                'photoUrls' => []
            ]
        );
    }

    /**
     * Update a pet in the petstore.
     *
     * @param int|string $id The pet ID
     * @param string $name The name of the pet to be updated
     * @param string $status The status of the pet to be updated
     *
     * @return Response The HTTP response
     */
    public function updatePet(int|string $id, string $name, string $status): Response
    {
        // Send a PUT request to the /pet endpoint with the pet ID, name, and status set.
        return $this->client()->put(
            $this->baseUrl . '/pet',
            [
                'id'        => $id,
                'name'      => $name,
                'status'    => $status,
                'photoUrls' => []
            ]
        );
    }

    /**
     * Delete a pet from the petstore.
     *
     * @param int|string $id The pet ID
     *
     * @return Response The HTTP response
     */
    public function deletePet(int|string $id): Response
    {
        // Send a DELETE request to the /pet/{id} endpoint to delete a pet.
        return $this->client()->delete(
            $this->baseUrl . '/pet/' . $id
        );
    }
}
