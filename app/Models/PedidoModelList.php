<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PedidoModelList extends Model
{
    // Configurações para o Eloquent do Laravel
    protected $table = 'pedidos'; // Nome da tabela no banco de dados
    public $incrementing = false; // Não incrementa o ID automaticamente
    protected $keyType = 'string'; // Tipo da chave primária
    protected $casts = [
        'id' => 'string', // Converte o ID para string
    ];

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
        'processando' => 'processando',
        'enviado' => 'enviado',
        'cancelado' => 'cancelado',
        'entregue' => 'entregue',
    ];

    const MAX_RETRIES = 3;
    const RETRY_DELAY = 100; // 100ms entre tentativas

    public function scopeListAll($query, $ordersPerPage = null, $page = null)
    {
        return retry(self::MAX_RETRIES, function() use ($query, $ordersPerPage, $page) {
            
            if ($ordersPerPage && $page) {
                return $query->paginate($ordersPerPage);
            }
            
            return $query->get();

            Log::channel('stderr')->info('>> PedidoModelList listAll falhou. Tentando novamente...');
        }, self::RETRY_DELAY); 
    }

    public function scopeListByStatus($query, $status)
    {
        $result = DB::table($this->table)->where('status', $status)->get();
        return $result;
    }

    public static function isValidStatus($status){
        
        $isValidStatus = array_key_exists($status, self::STATUS_TYPES);
        Log::channel('stderr')->info('>> PedidoModelList isValidStatus: ' . $isValidStatus);
        return $isValidStatus;
    }
    
    public static function isValidId($id){
        $isValidId = DB::table(self::TABLE)->where('id_usuario', $id)->exists();
        Log::channel('stderr')->info('>> PedidoModelList isValidId: ' . $isValidId);
        return $isValidId;
    }

    public function scopeGetPedidoByUserId($query, $userId){
        $pedido = DB::table($this->table)->where('id_usuario', $userId)->get();
        Log::channel('stderr')->info('>> PedidoModelList getPedidoByUserId: ' . $pedido);
        return $pedido;
    }

    public function scopeGetPedidoDetails($query, $pedidoId){
        $pedido = DB::table($this->table)->where('id', $pedidoId)->get();
        Log::channel('stderr')->info('>> PedidoModelList getPedidoDetails: ' . $pedido);
        return $pedido;
    }
}