<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoControllerCreate;
use App\Http\Controllers\PedidoControllerList;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/pedidos/create', [PedidoControllerCreate::class, 'create']);
Route::get('/pedidos/list', [PedidoControllerList::class, 'list']);
Route::get('/pedidos/list/{id}', [PedidoControllerList::class, 'list']);
// Route::put('/pedidos/update/{id}', [PedidoControllerCreate::class, 'update']);

// Route::get('/pedidos/itens/list', [PedidoController::class, 'listItens']);