<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\map;
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

        //listar todos os pedidos
        //paginação
        //filtragem por status

        $listType = (object) [
            'default' => 'all',
            'all_with_page' => 'page',
            'all_with_status' => 'status',
            'all' => 'all',
        ];

        $listByStatus = (object) [
            'default' => 'pendente',
            'processando' => 'processando',
            'enviado' => 'enviado',
            'cancelado' => 'cancelado',
            'entregue' => 'entregue',
        ];

        $listTypeReceived = $request->input('list_type');
        $ordersPerPageReceived = $request->input('orders_per_page');
        $listByStatusReceived = $request->input('list_by_status');  
        $pedidoIdUsuarioReceived = $request->input('pedido_id_usuario');
        $pageReceived = $request->input('page');

        $listTypeProcessed = $listTypeReceived ?? $listType->default;
        $listByStatusProcessed = $listByStatusReceived ?? $listByStatus->default;
        $ordersPerPageProcessed = $ordersPerPageReceived ?? null;
        $pedidoIdUsuarioProcessed = $pedidoIdUsuarioReceived ?? null;
        $pageProcessed = $pageReceived ?? null;

        Log::channel('stderr')->info('>> PedidoControllerList listTypeProcessed: ' . $listTypeProcessed);
        Log::channel('stderr')->info('>> PedidoControllerList listByStatusProcessed: ' . $listByStatusProcessed);
        Log::channel('stderr')->info('>> PedidoControllerList ordersPerPageProcessed: ' . $ordersPerPageProcessed);
        Log::channel('stderr')->info('>> PedidoControllerList pedidoIdUsuarioProcessed: ' . $pedidoIdUsuarioProcessed);

        function listAllOrders($listTypeParam, $listByStatusParam, $ordersPerPageParam, $pedidoIdUsuarioParam, $pageParam){

            if (!$ordersPerPageParam){
                Log::channel('stderr')->info('>> PedidoControllerList listAllOrders: Não foi passado o parametro ordersPerPage');
            }

            if ($ordersPerPageParam && !$pageParam){
                Log::channel('stderr')->info('>> PedidoControllerList listAllOrders: Não foi passado o parametro page');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Não foi passado o parametro page.',
                ], 400);
            }

            if ($ordersPerPageParam && $pageParam){
                $query = "SELECT * FROM pedidos LIMIT $ordersPerPageParam OFFSET $pageParam";
                $result = DB::table('pedidos')->get();
                return $result;
            }

            $query = "SELECT * FROM pedidos";
            $result = DB::table('pedidos')->get();

            return $result;
        }

        function listOrdersByStatus($listTypeParam, $listByStatusParam, $ordersPerPageParam, $pedidoIdUsuarioParam, $pageParam){
            $query = "SELECT * FROM pedidos WHERE status = $listByStatusParam";
            $result = DB::table('pedidos')->get();
            return $result;
        }

        $pedidosListados = null;

        if ($listTypeReceived == $listType->default){
            $pedidosListados = listAllOrders($listTypeProcessed, $listByStatusProcessed, $ordersPerPageProcessed, $pedidoIdUsuarioProcessed, $pageProcessed);
        }
        if ($listTypeReceived == $listType->all_with_page){
            $pedidosListados = listAllOrders($listTypeProcessed, $listByStatusProcessed, $ordersPerPageProcessed, $pedidoIdUsuarioProcessed, $pageProcessed);
        }
        if ($listTypeReceived == $listType->all_with_status){
            $pedidosListados = listOrdersByStatus($listTypeProcessed, $listByStatusProcessed, $ordersPerPageProcessed, $pedidoIdUsuarioProcessed, $pageProcessed);
        }
        
        
        return response()->json([
            'status' => 'success',
            'message' => 'Pedido listados com sucesso',
            'pedidos' => $pedidosListados
        ], 201);
    }

    // public function listItens(Request $request){
    //     Log::channel('stderr')->info('>> Requisição recebida em PedidoController.listItens. ');
    //     Log::channel('stderr')->info('>> PedidoController.listItens recebido: ' . json_encode($request->all()));

    //     $query = "SELECT * FROM estoque";
    //     $result = DB::table('estoque')->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Pedido itens listados com sucesso',
    //         'itens' => json_encode($result),
    //     ]);
    // }
}

