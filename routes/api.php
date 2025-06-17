<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoControllerCreate;
use App\Http\Controllers\PedidoControllerList;
use App\Http\Controllers\PedidoControllerUpdate;

Route::post('/pedidos/create', [PedidoControllerCreate::class, 'create']);
Route::get('/pedidos/list/{id?}', [PedidoControllerList::class, 'list']);
Route::put('/pedidos/update/', [PedidoControllerUpdate::class, 'updatePedidoStatus']);