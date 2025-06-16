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

    public function list(Request $request){
        Log::channel('stderr')->info('>> Requisição recebida em PedidoControllerList. ');
        Log::channel('stderr')->info('>> PedidoControllerList recebido: ' . json_encode($request->all()));

        $listType = $request->input('list_type', null);
        $status = $request->input('list_by_status', null);
        $ordersPerPage = $request->input('orders_per_page', null);
        $page = $request->input('page', null);
        $pedidoId = $request->input('pedido_id', null);
        $userId = $request->input('id_usuario', null);

        Log::channel('stderr')->info('>> PedidoControllerList listType: ' . $listType);
        Log::channel('stderr')->info('>> PedidoControllerList status: ' . $status);
        Log::channel('stderr')->info('>> PedidoControllerList ordersPerPage: ' . $ordersPerPage);
        Log::channel('stderr')->info('>> PedidoControllerList page: ' . $page);
        Log::channel('stderr')->info('>> PedidoControllerList pedidoId: ' . $pedidoId);
        Log::channel('stderr')->info('>> PedidoControllerList userId: ' . $userId);
        // Query base
        $query = PedidoModelList::query();

        if ($ordersPerPage && !$page) {
            return response()->json([
                'status' => 'error',
                'message' => 'Não foi passado o parametro page. Selecione um valor válido.',
            ], 400);
        }

        if ($page && !$ordersPerPage) {
            return response()->json([
                'status' => 'error',
                'message' => 'Não foi passado o parametro ordersPerPage. Selecione um valor válido.',
            ], 400);
        }

        // if (!in_array($status, PedidoModelList::STATUS_TYPES)) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Status inválido. Selecione um status válido.',
        //     ], 400);
        // }

        if ($userId) {
            Log::channel('stderr')->info('>> PedidoControllerList executando: listByUserId');
            
            if (!$userId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'id_usuario não informado.',
                ], 400);
            }
    
            Log::channel('stderr')->info('>> PedidoControllerList id_usuario: ' . $userId);
    
            if (!PedidoModelList::isValidId($userId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'id_usuario: ' . $userId . ' não encontrado.',
                ], 404);
            }
            
            $pedidos = PedidoModelList::query()->getPedidoByUserId($userId);
    
            if (!$pedidos) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pedido com id_usuario: ' . $userId . ' não encontrado.',
                ], 404);
            }
        }

        if ($pedidoId) {
            Log::channel('stderr')->info('>> PedidoControllerList executando: listByPedidoId');
            $pedidos = PedidoModelList::query()->getPedidoDetails($pedidoId);
        }   

        if ($page && $ordersPerPage) {
            Log::channel('stderr')->info('>> PedidoControllerList executando: listAll com paginação');
            $pedidos = PedidoModelList::query()->listAll($ordersPerPage, $page);
        }

        if (PedidoModelList::isValidStatus($status)) {
            Log::channel('stderr')->info('>> PedidoControllerList executando: listByStatus');
            $pedidos = PedidoModelList::query()->listByStatus($status);
        }

        if (array_key_exists($listType, PedidoModelList::LIST_TYPES)) {
            Log::channel('stderr')->info('>> PedidoControllerList executando: listAll com paginação');
            $pedidos = PedidoModelList::query()->listAll();
        }

        if (!$page && !$ordersPerPage && !$status && !$userId && !$pedidoId) {
            Log::channel('stderr')->info('>> PedidoControllerList executando: listAll simples');
            $pedidos = PedidoModelList::query()->listAll();
        }

        // Aplicar filtros baseado no tipo de listagem
        // if ($listType === PedidoModelList::LIST_TYPES['all_with_status']) {
        //     $query->listByStatus($status);
        // }

        
        

        return response()->json([
            'status' => 'success',
            'message' => 'Sucesso na requisição.',
            'details' => $pedidos
        ], 200);
    }

    public function listByPedidoId(Request $request, $pedidoId = null){
        Log::channel('stderr')->info('>> Requisição recebida em PedidoControllerList. listByPedidoId');
        Log::channel('stderr')->info('>> PedidoControllerList recebido: ' . json_encode($request->all()));

        if (!$pedidoId) {
            return response()->json([
                'status' => 'error',
                'message' => 'pedido_id não informado.',
            ], 400);
        }

        Log::channel('stderr')->info('>> PedidoControllerList pedido_id: ' . $pedidoId);

        $pedidos = PedidoModelList::query()->getPedidoDetails($pedidoId);

        if (!$pedidos) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pedido com pedido_id: ' . $pedidoId . ' não encontrado.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Pedido encontrado.',
            'details' => $pedidos
        ], 200);
    }
}

