<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/pedidos/create', [PedidoController::class, 'create']);
Route::get('/pedidos/list', [PedidoController::class, 'list']);
Route::get('/pedidos/list/{id}', [PedidoController::class, 'list']);
Route::put('/pedidos/update/{id}', [PedidoController::class, 'update']);

Route::get('/pedidos/itens/list', [PedidoController::class, 'listItens']);