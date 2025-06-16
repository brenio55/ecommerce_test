<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PedidoModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PedidoModelUpdate extends Model
{
    CONST TABLE = 'pedidos';

    const COLUNAS = PedidoModel::COLUNAS;

    const STATUS_TYPES = PedidoModel::STATUS_TYPES;

    const LIST_TYPES = PedidoModel::LIST_TYPES;

    public static function updatePedidoStatus($id, $status)
    {
        $pedido = self::find($id);

        if (!$pedido) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pedido não encontrado',
                'data' => []
            ], 404);
        }

        Log::channel('stderr')->info('>> Pedido encontrado. Atualizando status...');

        if (!array_key_exists(
            $status, self::STATUS_TYPES
            )) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Status inválido.',
                    'data' => []
                ], 400);
        }

        $currentPedidoStatus = DB::table(self::TABLE)
            ->where(self::COLUNAS['id'], $id)
            ->get();

        Log::channel('stderr')->info('>> Status atual do pedido: ' . $currentPedidoStatus);

        switch ($currentPedidoStatus) {
            // em caso de alteração de PENDENTE para outro status
            case (self::STATUS_TYPES['pendente']):
                if ($status == self::STATUS_TYPES['pendente']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Status já está no mesmo nível.',
                        'data' => []
                    ], 400);
                }

                if ($status == self::STATUS_TYPES['cancelado']) {
                    DB::transaction(function () use ($id, $status) {
                        DB::table(self::TABLE)
                            ->where(self::COLUNAS['id'], $id)
                            ->update([
                                self::COLUNAS['status'] => $status
                            ]);
                    });
                }
                
                if ($status == self::STATUS_TYPES['processando']) {
                    DB::transaction(function () use ($id, $status) {
                        DB::table(self::TABLE)
                            ->where(self::COLUNAS['id'], $id)
                            ->update([
                                self::COLUNAS['status'] => $status
                            ]);
                    });
                }
                break;
            // em caso de alteração de PROCESSANDO para outro status
            case (self::STATUS_TYPES['processando']):
                if ($status == self::STATUS_TYPES['cancelado']) {
                    DB::transaction(function () use ($id, $status) {
                        DB::table(self::TABLE)
                            ->where(self::COLUNAS['id'], $id)
                            ->update([
                                self::COLUNAS['status'] => $status
                            ]);
                    });
                }
                break;
            // em caso de alteração de CANCELADO para outro status
            case (self::STATUS_TYPES['cancelado']):
                if (!$status == self::STATUS_TYPES['cancelado']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Status não pode ser alterado de cancelado para outro status.',
                        'data' => []
                    ], 403);
                }

                if ($status == self::STATUS_TYPES['cancelado']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Status já está no mesmo nível.',
                        'data' => []
                    ], 400);
                }
            break;
            // em caso de alteração de ENVIADO para outro status
            case (self::STATUS_TYPES['enviado']):
                if ($status == self::STATUS_TYPES['cancelado']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Status não pode ser alterado de enviado para outro cancelado.',
                        'data' => []
                    ], 403);
                }
                break;
            // em caso de alteração de ENTREGUE para outro status
            case (self::STATUS_TYPES['entregue']):
                if ($status == self::STATUS_TYPES['cancelado']) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Status não pode ser alterado de entregue para outro cancelado.',
                        'data' => []
                    ], 403);
                }
                break;
        }

        return $pedido;
    }
}
