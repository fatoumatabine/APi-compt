<?php
/**
 * @OA\Info(
 *     title="API Gestion de Comptes",
 *     version="1.0",
 *     description="API pour la gestion des comptes bancaires"
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Serveur local"
 * )
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('register', [App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('login', [App\Http\Controllers\API\AuthController::class, 'login']);

    Route::apiResource('comptes', App\Http\Controllers\API\CompteController::class);
    Route::apiResource('clients', App\Http\Controllers\API\ClientController::class);
    Route::apiResource('admins', App\Http\Controllers\API\AdminController::class);
});
