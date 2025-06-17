<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PedidoModelList;
use App\Models\PedidoModelUpdate;
use App\Models\PedidoModel;

class PedidoControllerUpdate extends Controller
{
    public function updatePedidoStatus(Request $request)
    {
        Log::channel('stderr')->info('>> Requisição recebida em PedidoControllerUpdate');
        Log::channel('stderr')->info('>> Parâmetros: ' . json_encode($request->all()));

        $idReceived = $request->input(PedidoModel::COLUNAS['id']);
        $statusReceived = $request->input(PedidoModel::COLUNAS['status']);

        Log::channel('stderr')->info('>> ID recebido: ' . $idReceived);
        Log::channel('stderr')->info('>> Status recebido: ' . $statusReceived);

        $pedido = PedidoModelUpdate::updatePedidoStatus($idReceived, $statusReceived);

        if (is_a($pedido, 'Illuminate\Http\JsonResponse')) {
            return $pedido; // Retorna a resposta de erro se houver
        }

        if (!$pedido || (is_array($pedido) && empty($pedido))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nenhum pedido encontrado',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status do pedido atualizado com sucesso',
            'data' => $pedido
        ]);
    }
}
