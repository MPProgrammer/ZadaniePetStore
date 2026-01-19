<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PetstoreService;
use Illuminate\Validation\Rule;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class PetController extends Controller
{
    public function index()
    {
        return view('pets.index', [
            'statuses' => config('pets.statuses'),
        ]);
    }

    public function create()
    {
        return view('pets.create', [
            'statuses' => config('pets.statuses'),
        ]);
    }

    public function edit(int|string $id, PetstoreService $service)
    {
        $response = $service->getPet($id);

        if ($response->failed()) {
            return redirect()
                ->route('pets.index')
                ->withErrors('Pet not found');
        }

        return view('pets.edit', [
            'pet' => $response->json(),
            'statuses' => config('pets.statuses'),
        ]);
    }



    public function ajaxList(Request $request, PetstoreService $service)
    {
        // $request->validate([
        //     'status' => [
        //         'nullable',
        //         Rule::in(array_keys(config('pets.statuses'))),
        //     ],
        // ]);
        $request->validate([
            'status' => Rule::in(array_keys(config('pets.statuses'))),
        ]);

        // $validator = Validator::make($request->all(), [
        //     'status' => Rule::in(array_keys(config('pets.statuses'))),
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'error' => $validator->errors()->first('status')
        //     ], 422);
        // }

        $status = $request->get('status', 'available');

        $cacheKey = 'pets_status_' . $status;

        $pets = Cache::remember($cacheKey, now()->addMinutes((int) config('pets.cache_duration_minutes')), function () use ($service, $status) {
            $response = $service->findByStatus($status);

            if ($response->failed()) {
                return null;
            }

            return $response->json();
        });

        if ($pets === null) {
            return response()->json([
                'error' => 'Petstore API error'
            ], 500);
        }

        // roblem z zaokragleniem dużych liczb w JS - ID jako string
        $pets = array_map(function ($pet) {
            if (isset($pet['id'])) {
                $pet['id'] = (string) $pet['id'];
            }
            return $pet;
        }, $pets);

        return response()->json($pets);
    }

    public function store(Request $request, PetstoreService $service)
    {
        $data = $request->validate([
            // 'id' => 'required|integer',
            'name' => 'required|string',
            'status' => Rule::in(array_keys(config('pets.statuses'))),
        ]);

        $response = $service->createPet($data);

        // var_dump($response->body()); exit;

        if ($response->failed()) {
            return back()
                ->withErrors('Unable to create pet')
                ->withInput();
        }

        $this->clearPetsCache();

        $id = $response->json()['id'];

        return redirect()
            ->route('pets.edit', $id)
            ->with('success', 'Pet created successfully');
    }

    public function update(int $id, Request $request, PetstoreService $service)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'status' => Rule::in(array_keys(config('pets.statuses'))),
        ]);

        $response = $service->updatePet([
            'id' => $id,
            ...$data
        ]);

        if ($response->failed()) {
            return back()->withErrors('Unable to update pet');
        }

        $this->clearPetsCache();

        return back()->with('success', 'Pet updated');
    }

    public function destroy(int $id, PetstoreService $service)
    {
        $response = $service->deletePet($id);

        if ($response->failed()) {
            return redirect()
                ->route('pets.index')
                ->withErrors('Unable to delete pet');
        }

        $this->clearPetsCache();

        return redirect()
            ->route('pets.index')
            ->with('success', 'Pet deleted');
    }

    public function destroyAjax(string $id, PetstoreService $service)
    {
        $response = $service->deletePet($id);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Unable to delete pet'
            ], 500);
        }

        $this->clearPetsCache();

        return response()->json([
            'success' => true
        ]);
    }




    // public function destroy(int $id, PetstoreService $service)
    // {
    //     $response = $service->deletePet($id);

    //     if ($response->failed()) {
    //         return response()->json([
    //             'error' => 'Unable to delete pet'
    //         ], 500);
    //     }

    //     $this->clearPetsCache();

    //     return response()->json([
    //         'success' => true
    //     ]);
    // }

    private function clearPetsCache(): void
    {
        foreach (array_keys(config('pets.statuses')) as $status) {
            Cache::forget('pets_status_' . $status);
        }
    }
}
