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

Route::apiResource('cliente', ClienteController::class)->middleware('jwt.auth');
Route::apiResource('carro', CarroController::class)->middleware('jwt.auth');
Route::apiResource('modelo', ModeloController::class)->middleware('jwt.auth');
Route::apiResource('marca', MarcaController::class)->middleware('jwt.auth');
Route::apiResource('locacao', LocacaoController::class)->middleware('jwt.auth');

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('me', [AuthController::class, 'me']);

