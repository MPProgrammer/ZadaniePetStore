<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PetstoreService;

class PetController extends Controller
{
    public function index()
    {
        return view('pets.index');
    }

    public function ajaxList(Request $request, PetstoreService $service)
    {
        $status = $request->get('status', 'available');

        $response = $service->findByStatus($status);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Petstore API error'
            ], 500);
        }

        return response()->json($response->json());
    }
}
