<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\PedidoModelList;
use Illuminate\Support\Str;



/**
 *  * Cria um novo pedido na base de dados
 *
 * @param Request $request
 * Espera um JSON com o seguinte formato:
 * {
 *   "cliente_id": int,
 *   "itens": [
 *     {
 *       "produto_id": int,
 *       "quantidade": int
 *     }
 *   ],
 *   "observacoes": string|null
 * }
 * 
 * ID do pedido criado É uuid no banco de dados automático
 *
 * @return \Illuminate\Http\JsonResponse
 * Retorna um JSON com o status e dados do pedido criado
 * 
 */

class PedidoControllerList extends Controller
{
    public function list(Request $request, $id = null)
    {
        Log::channel('stderr')->info('>> Requisição recebida em PedidoControllerList');
        Log::channel('stderr')->info('>> Parâmetros: ' . json_encode($request->all()));

        if ($id) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pedido encontrado com sucesso',
                'data' => PedidoModelList::find($id)
            ]);
        }
        
        $data = PedidoModelList::aplicarFiltros($request);

        if (is_a($data, 'Illuminate\Http\JsonResponse')) {
            return $data; // Retorna a resposta de erro se houver
        }

        if (!$data || (is_array($data) && empty($data))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nenhum pedido encontrado',
                'data' => []
            ], 404);
        }

        return response()->json([
            'status' => 'success', 
            'message' => 'Pedidos listados com sucesso',
            'data' => $data
        ]);
    }
}

