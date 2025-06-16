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

        $pedido = PedidoModelUpdate::updatePedidoStatus($idReceived, $statusReceived);

        return response()->json([
            'status' => 'success',
            'message' => 'Status do pedido atualizado com sucesso',
            'data' => $pedido
        ]);
            
        
        return response()->json([
            'status' => 'error',
            'message' => 'Status do pedido não atualizado. Erro interno.',
            'data' => []
        ], 500);
    }
}
