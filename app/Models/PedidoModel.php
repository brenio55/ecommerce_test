<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoModel extends Model
{
    CONST TABLE = 'pedidos';

    // Constants para tipos de listagem
    const LIST_TYPES = [
        'default' => 'all',
        'all_with_page' => 'page',
        'all_with_status' => 'status',
        'all' => 'all',
    ];

    const STATUS_TYPES = [
        'default' => 'pendente',
        'pendente' => 'pendente',
        'processando' => 'processando',
        'enviado' => 'enviado',
        'cancelado' => 'cancelado',
        'entregue' => 'entregue',
    ];

    const COLUNAS = [
        'id' => 'id',
        'id_usuario' => 'id_usuario',
        'status' => 'status',
        'data_pedido' => 'data_pedido',
        'total_valor' => 'total_valor',
    ];
}
