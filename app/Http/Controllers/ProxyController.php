<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    private $site2Url;
    private $site2Secret;

    public function __construct()
    {
        $this->site2Url = env('USERS2_SERVICE_BASE_URL');
        $this->site2Secret = env('USERS2_SERVICE_SECRET');
    }

    public function getProducts()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->site2Secret,
        ])->get($this->site2Url . '/products');

        return response()->json($response->json(), $response->status());
    }

    public function showProduct($id)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->site2Secret,
        ])->get($this->site2Url . '/products/' . $id);

        return response()->json($response->json(), $response->status());
    }

    public function storeProduct(\Illuminate\Http\Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->site2Secret,
        ])->post($this->site2Url . '/products', $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function updateProduct(\Illuminate\Http\Request $request, $id)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->site2Secret,
        ])->put($this->site2Url . '/products/' . $id, $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function destroyProduct($id)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->site2Secret,
        ])->delete($this->site2Url . '/products/' . $id);

        return response()->json($response->json(), $response->status());
    }
} 