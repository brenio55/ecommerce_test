<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoControllerCreate;
use App\Http\Controllers\PedidoControllerList;

Route::post('/pedidos/create', [PedidoControllerCreate::class, 'create']);
Route::get('/pedidos/list', [PedidoControllerList::class, 'list']);
Route::get('/pedidos/list/{pedido_id}', [PedidoControllerList::class, 'listByPedidoId']);

// Route::put('/pedidos/update/{id}', [PedidoControllerCreate::class, 'update']);