<?php

/**
 * @author    Panek Mariusz <mariusz@codelarix.dev>
 * @copyright Copyright © Codelarix (https://codelarix.dev)
 * @version   1.0.0
 */

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Services\PetstoreService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class PetController extends Controller
{
    /**
     * Show the list of pets.
     *
     * @return View The view instance
     */
    public function index(): View
    {
        // Get the list of statuses from the config file
        // and pass it to the view
        return view('pets.index', [
            'statuses' => config('pets.statuses')
        ]);
    }

    /**
     * Show the create page for a pet.
     *
     * @return View The view instance
     */
    public function create(): View
    {
        // Get the list of statuses from the config file
        // and pass it to the view
        return view('pets.create', [
            'statuses' => config('pets.statuses')
        ]);
    }

    /**
     * Show the edit page for a pet.
     *
     * @param int|string $id The pet ID
     * @param PetstoreService $service The petstore service
     *
     * @return View|RedirectResponse The view instance or a redirect response if the pet is not found
     */
    public function edit(int|string $id, PetstoreService $service): View|RedirectResponse
    {
        // Get the pet by its ID
        $response = $service->getPet($id);

        // If the pet is not found, redirect to the list of pets with an error message
        if ($response->failed()) {
            return redirect()
                ->route('pets.index')
                ->withErrors('Pet not found');
        }

        // Get the pet data from the response
        $data = $response->json();

        // Pass the pet data and the list of statuses to the view
        return view('pets.edit', [
            'pet'      => [
                'id'     => (string) $data['id'],
                'name'   => $data['name'] ?? '',
                'status' => $data['status']
            ],
            'statuses' => config('pets.statuses')
        ]);
    }

    /**
     * Return a list of pets for the given status.
     *
     * The list is cached for the duration specified in the config file.
     *
     * @param Request $request The HTTP request
     * @param PetstoreService $service The petstore service
     *
     * @return JsonResponse The JSON response
     */
    public function ajaxList(Request $request, PetstoreService $service): JsonResponse
    {
        // Validate the request data
        $request->validate([
            'status' => Rule::in(array_keys(config('pets.statuses')))
        ]);

        // Get the status from the request
        $status = $request->get('status', 'available');

        // Create a cache key based on the status
        $cacheKey = 'pets_status_' . $status;

        // Get the list of pets from the cache or from the API
        $pets = Cache::remember($cacheKey, now()->addMinutes((int) config('pets.cache_duration_minutes')), function () use ($service, $status) {
            // Get the list of pets from the API
            $response = $service->findByStatus($status);

            // If the API returns an error, return null
            if ($response->failed()) {
                return null;
            }

            // Return the list of pets as JSON
            return $response->json();
        });

        // If the list of pets is null, return an error response
        if ($pets === null) {
            return response()->json([
                'error' => 'Petstore API error'
            ], 500);
        }

        // Convert the IDs of the pets to strings to avoid issues with large numbers in JavaScript
        $pets = array_map(function ($pet) {
            if (isset($pet['id'])) {
                $pet['id'] = (string) $pet['id'];
            }
            return $pet;
        }, $pets);

        // Return the list of pets as JSON
        return response()->json($pets);
    }

    /**
     * Store a new pet in the petstore.
     *
     * @param Request $request The HTTP request
     * @param PetstoreService $service The petstore service
     *
     * @return RedirectResponse The redirect response
     */
    public function store(Request $request, PetstoreService $service): RedirectResponse
    {
        // Validate the request data
        $data = $request->validate([
            'name'   => 'required|string',
            'status' => [
                'required',
                Rule::in(array_keys(config('pets.statuses')))
            ]
        ]);

        // Create a new pet in the petstore
        $response = $service->createPet((string) $data['name'], (string) $data['status']);

        // If the petstore API returns an error, redirect back to the create page with an error message
        if ($response->failed()) {
            return back()
                ->withErrors('Unable to create pet')
                ->withInput();
        }

        // Clear the cache of pets
        $this->clearPetsCache();

        // Get the ID of the newly created pet
        $id = $response->json()['id'];

        // Redirect to the edit page of the newly created pet with a success message
        return redirect()
            ->route('pets.edit', $id)
            ->with('success', 'Pet created successfully');
    }

    /**
     * Update a pet in the petstore.
     *
     * @param int $id The pet ID
     * @param Request $request The HTTP request
     * @param PetstoreService $service The petstore service
     *
     * @return RedirectResponse The redirect response
     */
    public function update(int $id, Request $request, PetstoreService $service): RedirectResponse
    {
        // Validate the request data
        // The name and status of the pet must be present in the request data
        // The name must be a string and the status must be one of the allowed statuses
        $data = $request->validate([
            'name'   => 'required|string',
            'status' => Rule::in(array_keys(config('pets.statuses')))
        ]);

        // Update the pet in the petstore
        // Send a PUT request to the /pet/{id} endpoint with the name and status set
        $response = $service->updatePet($id, (string) $data['name'], (string) $data['status']);

        // If the petstore API returns an error, redirect back to the edit page with an error message
        if ($response->failed()) {
            return back()->withErrors('Unable to update pet');
        }

        // Clear the cache of pets
        $this->clearPetsCache();

        // Redirect back to the edit page with a success message
        return back()->with('success', 'Pet updated');
    }

    /**
     * Delete a pet from the petstore.
     *
     * @param int $id The pet ID
     * @param PetstoreService $service The petstore service
     *
     * @return RedirectResponse The redirect response
     */
    public function destroy(int $id, PetstoreService $service): RedirectResponse
    {
        // Send a DELETE request to the /pet/{id} endpoint to delete a pet.
        $response = $service->deletePet($id);

        // If the petstore API returns an error, redirect back to the list page with an error message
        if ($response->failed()) {
            return redirect()
                ->route('pets.index')
                ->withErrors('Unable to delete pet');
        }

        // Clear the cache of pets
        $this->clearPetsCache();

        // Redirect back to the list page with a success message
        return redirect()
            ->route('pets.index')
            ->with('success', 'Pet deleted');
    }

    /**
     * Delete a pet from the petstore via an AJAX request.
     *
     * @param int|string $id The pet ID
     * @param PetstoreService $service The petstore service
     *
     * @return JsonResponse The JSON response
     */
    public function ajaxDestroy(int|string $id, PetstoreService $service): JsonResponse
    {
        // Send a DELETE request to the /pet/{id} endpoint to delete a pet.
        $response = $service->deletePet($id);

        // If the petstore API returns an error, return an error response
        if ($response->failed()) {
            return response()->json([
                'error' => 'Unable to delete pet'
            ], 500);
        }

        // Clear the cache of pets
        $this->clearPetsCache();

        // Return a success response
        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Clear the cache of pets.
     *
     * This method clears the cache of all pets, regardless of their status.
     * It loops through all the statuses defined in the config file and
     * removes the cache key for each status.
     */
    private function clearPetsCache(): void
    {
        // Loop through all the statuses defined in the config file
        foreach (array_keys(config('pets.statuses')) as $status) {
            // Remove the cache key for the given status
            Cache::forget('pets_status_' . $status);
        }
    }
}
