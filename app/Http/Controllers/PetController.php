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

        $pets = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($service, $status) {
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

        return response()->json($pets);
    }
}
