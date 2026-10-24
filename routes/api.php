<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;

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
