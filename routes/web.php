<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;


Route::get('/', function () {
    return view('welcome');
});


if (app()->environment('local')) {
    Route::get('/fetch-dms-users', function () {

        $url = rtrim(config('services.dms.url'), '/');

        // STEP 1: Get access token from DMS
        $token = Http::asForm()
            ->post($url . '/oauth/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.dms.client_id'),
                'client_secret' => config('services.dms.client_secret'),
                'scope' => 'documents:read',
            ])
            ->throw()
            ->json('access_token');

        // STEP 2: Fetch users from DMS
        $response = Http::withToken($token)
            ->get($url . '/api/users')
            ->throw();

        // STEP 3: Return users
        return response()->json($response->json());
    });
}
