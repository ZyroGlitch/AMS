<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\CheckToken;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;

// Local development test route: GET /api/dms-fetch-users on AMS.
if (app()->environment('local')) {
    Route::get('/dms-fetch-users', function () {
        $url = rtrim(config('services.dms_api.url'), '/');

        $login = Http::acceptJson()->timeout(10)->post($url . '/api/login', [
            'email' => config('services.dms_api.email'),
            'password' => config('services.dms_api.password'),
        ]);

        if ($login->failed()) {
            return response()->json([
                'message' => 'DMS login failed',
                'status' => $login->status(),
            ], 502);
        }

        $token = $login->json('access_token');

        if (!is_string($token) || $token === '') {
            return response()->json(['message' => 'DMS did not return an access token'], 502);
        }

        $response = Http::acceptJson()->withToken($token)->timeout(10)
            ->get($url . '/api/users');

        return response()->json([
            'system' => 'Accounting Management System',
            'DMS_response' => $response->json(),
        ], $response->status());
    });
}

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

Route::get('/accounting/test', function () {

    return response()->json([
        'success' => true,
        'message' => 'DMS successfully connected to AMS!',
        'system' => 'Accounting Management System',
    ]);
})->middleware(
    EnsureClientIsResourceOwner::using('accounting:read')
);

Route::middleware(EnsureClientIsResourceOwner::using('documents:read'))->group(function () {
    Route::get('/users', function () {

        $users = DB::table('users')
            ->select('id', 'name', 'email')
            ->get();

        return response()->json([
            'system' => 'Accounting Management System',
            'users' => $users,
        ]);
    });
});


// -------------------------------------------------------------------------
// LARAVEL PASSPORT API (Individual User Auth)
// Public login
Route::post('/login', [ApiAuthController::class, 'login'])->middleware('throttle:5,1');

// Protected AMS routes
Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [ApiAuthController::class, 'logout']);

    Route::middleware(CheckToken::using('accounting:read'))->group(function () {

        Route::get('/users', function () {
            $users = DB::table('users')
                ->select('id', 'name', 'email')
                ->get();

            return response()->json([
                'system' => 'Accounting Management System',
                'users' => $users,
            ]);
        });
    });
});
