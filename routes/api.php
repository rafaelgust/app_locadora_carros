<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarroController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\LocacaoController;

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

Route::get('/', function () {
    return ['Chegou aqui' => 'Sim'];        
});

Route::prefix('v1')->middleware('jwt.auth')->group( function () {
    Route::apiResource('cliente', ClienteController::class);
    Route::apiResource('carro', CarroController::class);
    Route::apiResource('modelo', ModeloController::class);
    Route::apiResource('marca', MarcaController::class);
    Route::apiResource('locacao', LocacaoController::class);
});

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
Route::post('refresh', [AuthController::class, 'refresh'])->middleware('jwt.auth');
Route::post('me', [AuthController::class, 'me'])->middleware('jwt.auth');

