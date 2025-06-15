<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PedidoModelList extends Model
{
    protected $table = 'pedidos';

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
        }, self::RETRY_DELAY); // 100ms entre tentativas
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
}