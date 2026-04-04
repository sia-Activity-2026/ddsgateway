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

    private function getSite2Token()
    {
        $response = Http::post($this->site2Url . '/oauth/token', [
            'grant_type' => 'client_credentials',
            'client_id' => 3,  // Use the client_credentials client ID from ddsbe2 DB
            'client_secret' => $this->site2Secret,
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        return null;  // Handle error if token fetch fails
    }

    public function getProducts()
    {
        $token = $this->getSite2Token();
        if (!$token) {
            return response()->json(['error' => 'Unable to authenticate'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get($this->site2Url . '/products');

        return response()->json($response->json(), $response->status());
    }

    public function showProduct($id)
    {
        $token = $this->getSite2Token();
        if (!$token) {
            return response()->json(['error' => 'Unable to authenticate'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get($this->site2Url . '/products/' . $id);

        return response()->json($response->json(), $response->status());
    }

    public function storeProduct(\Illuminate\Http\Request $request)
    {
        $token = $this->getSite2Token();
        if (!$token) {
            return response()->json(['error' => 'Unable to authenticate'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post($this->site2Url . '/products', $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function updateProduct(\Illuminate\Http\Request $request, $id)
    {
        $token = $this->getSite2Token();
        if (!$token) {
            return response()->json(['error' => 'Unable to authenticate'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put($this->site2Url . '/products/' . $id, $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function destroyProduct($id)
    {
        $token = $this->getSite2Token();
        if (!$token) {
            return response()->json(['error' => 'Unable to authenticate'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete($this->site2Url . '/products/' . $id);

        return response()->json($response->json(), $response->status());
    }
}