<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PedidoModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PedidoModelUpdate extends Model
{
    CONST TABLE = PedidoModel::TABLE;
    protected $table = self::TABLE;

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
            ->select(self::COLUNAS['status'])
            ->first();

        Log::channel('stderr')->info('>> Status atual do pedido: ' . $currentPedidoStatus->status);
        Log::channel('stderr')->info('>> Status passado para atualização: ' . $status);

        // Status Atual	     Próximos Status Permitidos
        // pendente	         processando, cancelado
        // processando	     enviado, cancelado
        // enviado	         entregue
        // entregue	         - (finalizado, sem transições)
        // cancelado	     - (finalizado, sem transições)

        try {
            switch ($currentPedidoStatus->status) {
                // em caso de alteração de PENDENTE para PROCESSANDO ou CANCELADO
                case (self::STATUS_TYPES['pendente']):
                    Log::channel('stderr')->info('>> Alteração de PENDENTE para outro status');
                    if ($status == self::STATUS_TYPES['pendente']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status já está no mesmo nível.',
                            'data' => []
                        ], 400);
                    }

                    if ($status !== self::STATUS_TYPES['pendente'] && $status !== self::STATUS_TYPES['processando'] && $status !== self::STATUS_TYPES['cancelado']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status do pedido não pode ser alterado de pendente para ' . $status,
                            'data' => []
                        ], 403);
                    }

                    if ($status == self::STATUS_TYPES['cancelado'] || $status == self::STATUS_TYPES['processando']) {
                        Log::channel('stderr')->info('>> Atualizando status do pedido para ' . $status);
                        $query = DB::table(self::TABLE)
                            ->where(self::COLUNAS['id'], $id)
                            ->update([
                                self::COLUNAS['status'] => $status
                            ]);

                        if (!$query) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Status do pedido não atualizado.',
                                'data' => []
                            ], 400);
                        }

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Status do pedido atualizado com sucesso para ' . $status                        
                        ]);
                    }
                    break;
                // em caso de alteração de PROCESSANDO para outro status
                case (self::STATUS_TYPES['processando']):
                    Log::channel('stderr')->info('>> Alteração de PROCESSANDO para outro status');
                    if ($status == self::STATUS_TYPES['processando']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status já está no mesmo nível.',
                            'data' => []
                        ], 400);
                    }

                    if ($status !== self::STATUS_TYPES['processando'] && $status !== self::STATUS_TYPES['enviado'] && $status !== self::STATUS_TYPES['cancelado']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status do pedido não pode ser alterado de processando para ' . $status,
                            'data' => []
                        ], 403);
                    }

                    if ($status == self::STATUS_TYPES['enviado'] || $status == self::STATUS_TYPES['cancelado']) {
                        Log::channel('stderr')->info('>> Atualizando status do pedido para ' . $status);
                        $query = DB::table(self::TABLE)
                            ->where(self::COLUNAS['id'], $id)
                            ->update([
                                self::COLUNAS['status'] => $status
                            ]);

                        if (!$query) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Status do pedido não atualizado.',
                                'data' => []
                            ], 400);
                        }

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Status do pedido atualizado com sucesso para ' . $status                        
                        ]);
                    }
                    break;
                // em caso de alteração de CANCELADO para outro status
                case (self::STATUS_TYPES['cancelado']):
                    Log::channel('stderr')->info('>> Alteração de CANCELADO para outro status');
                    if ($status == self::STATUS_TYPES['cancelado']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status já está no mesmo nível.',
                            'data' => []
                        ], 403);
                    }

                    if ($status !== self::STATUS_TYPES['cancelado'] && $status !== self::STATUS_TYPES['processando']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status do pedido não pode ser alterado de cancelado para ' . $status,
                            'data' => []
                        ], 403);
                    }
                break;
                // em caso de alteração de ENVIADO para outro status
                case (self::STATUS_TYPES['enviado']):
                    Log::channel('stderr')->info('>> Alteração de ENVIADO para outro status');
                    if ($status == self::STATUS_TYPES['enviado']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status já está no mesmo nível.',
                            'data' => []
                        ], 400);
                    }

                    if ($status !== self::STATUS_TYPES['enviado'] && $status !== self::STATUS_TYPES['entregue']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status do pedido não pode ser alterado de enviado para ' . $status,
                            'data' => []
                        ], 403);
                    }

                    if ($status == self::STATUS_TYPES['entregue']) {
                        Log::channel('stderr')->info('>> Atualizando status do pedido para ' . $status);
                        $query = DB::table(self::TABLE)
                            ->where(self::COLUNAS['id'], $id)
                            ->update([
                                self::COLUNAS['status'] => $status
                            ]);

                        if (!$query) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Status do pedido não atualizado.',
                                'data' => []
                            ], 400);
                        }

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Status do pedido atualizado com sucesso para ' . $status                        
                        ]);
                    }
                    break;
                // em caso de alteração de ENTREGUE para outro status
                case (self::STATUS_TYPES['entregue']):
                    Log::channel('stderr')->info('>> Alteração de ENTREGUE para outro status');
                    if ($status == self::STATUS_TYPES['entregue']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status já está no mesmo nível.',
                            'data' => []
                        ], 403);
                    }

                    if ($status !== self::STATUS_TYPES['entregue'] && $status !== self::STATUS_TYPES['cancelado']) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Status do pedido não pode ser alterado de entregue para ' . $status,
                            'data' => []
                        ], 403);
                    }
                    break;
                default:
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Status do pedido não pode ser alterado para ' . $status . ' Houve um erro.',
                        'data' => []
                    ], 500);
            }
        } catch (\Exception $e) {
            Log::channel('stderr')->error('>> Erro ao atualizar status do pedido: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar status do pedido: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
        
        return $pedido;
    }
}
