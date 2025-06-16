<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PedidoModel;

class PedidoModelList extends Model
{
    // Configurações para o Eloquent do Laravel
    protected $table = 'pedidos'; // Nome da tabela no banco de dados
    public $incrementing = false; // Não incrementa o ID automaticamente
    protected $keyType = 'string'; // Tipo da chave primária
    
    const COLUNAS = PedidoModel::COLUNAS;

    //direction = 'asc' ou 'desc'
    const DIRECTION = [
        'direction' => 'direction',
        'asc' => 'asc',
        'desc' => 'desc',
    ];

    const ORDER_BY = [
        'data_pedido' => 'data_pedido',
        'total_valor' => 'total_valor',
    ];
    
    public static function aplicarFiltros($request)
    {
        $query = self::query();

        if ($request->has('order_by') && !$request->has('direction')) {
            return response()->json([
                'status' => 'error',
                'message' => 'O parâmetro direction é obrigatório quando order_by é informado',
                
            ], 400);
        }

        if (!$request->has('per_page')) {
            return $query
            ->aplicarFiltroStatus($request->input(self::COLUNAS['status']))
            ->aplicarFiltroUsuario($request->input(self::COLUNAS['id_usuario']))
            ->aplicarOrdenacao($request->input('order_by'), $request->input(self::DIRECTION['direction']))
            ->get();
        }

        if ($request->has('per_page')) {
            return $query
            ->aplicarFiltroStatus($request->input(self::COLUNAS['status']))
            ->aplicarFiltroUsuario($request->input(self::COLUNAS['id_usuario']))
            ->aplicarOrdenacao($request->input('order_by'), $request->input(self::DIRECTION['direction']))
            ->paginate($request->input('per_page', 10));
        }
    }

    
    public function scopeAplicarFiltroStatus($query, $status = null)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    
    public function scopeAplicarFiltroUsuario($query, $usuarioId = null)
    {
        if ($usuarioId) {
            return $query->where('id_usuario', $usuarioId);
        }
        return $query;
    }

    // Ordenação
    public function scopeAplicarOrdenacao($query, $orderBy = null, $direction = 'desc')
    {
        $camposPermitidos = ['data_pedido', 'total_valor'];
        
        if ($orderBy && in_array($orderBy, $camposPermitidos)) {
            return $query->orderBy($orderBy, $direction);
        }
        
        return $query->orderBy('data_pedido', 'desc'); // ordenação padrão
    }
}