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

class PedidoController extends Controller
{

    // public function accessSupabaseDatabase(){
    //     $supabase = new Client(
    //         env('DB_URL'),
    //         env('DB_ANON_KEY')
    //     );
    //     return $supabase;
    // }

    // private function checkUserExists($userId){
    //     $query = "SELECT * FROM usuarios WHERE id = $userId";
    //     $result = DB::table('usuarios')->where('id', $userId)->first();
    //     return $result;
    // }

    public function create(Request $request){
        Log::channel('stderr')->info('>> Requisição recebida em PedidoController. ');
        Log::channel('stderr')->info('>> PedidoController recebido: ' . json_encode($request->all()));


        $pedidoIdUsuario = $request->input('pedido_id_usuario');
        $pedidoItens = $request->input('pedido_itens');
        $pedidoItens = json_decode($pedidoItens, true);

        function organizeItems($pedidoItensParam){
            // Faz o log no formato desejado
            $localPedidoItensParam = $pedidoItensParam;
            Log::channel('stderr')->info('>> PedidoController pedidoItens decoded: ' . json_encode($localPedidoItensParam, JSON_PRETTY_PRINT));

            // Continua o processamento com o map
            $pedidoItens = collect($localPedidoItensParam)->map(function($item) {
                return [
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'valor_unitario_atTheTime' => $item['valor_unitario_atTheTime'],
                ];
            })->toArray();

            // $pedidoItens = json_encode($pedidoItens);
            

            Log::channel('stderr')->info('>> PedidoController localPedidoItensParam mapped: ' . json_encode($localPedidoItensParam, JSON_PRETTY_PRINT));
            $itensOrganized = $localPedidoItensParam;

            return $itensOrganized;
        }

        function returnItemsAndQuantities($pedidoItensParam){
            // Log::channel('stderr')->info('>> PedidoController returnItemsAndQuantities: retornando quantidade dos itens do pedido.');
            $localPedidoItensParam = $pedidoItensParam;
            $itemsAndQuantities = collect($localPedidoItensParam)->map(function($item) {
                return [
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                ];
            })->toArray();


            // Log::channel('stderr')->info('>> PedidoController itemsAndQuantities: ' . json_encode($itemsAndQuantities, JSON_PRETTY_PRINT));

            return $itemsAndQuantities;
        }

        function calculateItemsTotal($pedidoItensParam){
            Log::channel('stderr')->info('>> PedidoController calculateItemsTotal: calculando total dos itens do pedido.');

            $localPedidoItensParam = $pedidoItensParam;

            $itemsTotal = collect($localPedidoItensParam)->map(function($item) {
                return $item['quantidade'] * $item['valor_unitario_atTheTime'];
            })->sum();
            

            Log::channel('stderr')->info('>> PedidoController itemsTotal: ' . $itemsTotal);

            return $itemsTotal;
        }

        function verifyItemsInStock($pedidoItensParam, $quantidadeItensParam) {
            Log::channel('stderr')->info('>> PedidoController verifyItemsInStock: verificando se item tem em estoque.');

            $itensForaEstoque = [];

            foreach ($pedidoItensParam as $item) {
                Log::channel('stderr')->info('>> PedidoController verifyItemsInStock conectando ao banco de dados.');
                $quantidadeEmEstoque = DB::table('estoque')
                    ->where('id', $item['produto_id'])
                    ->value('quantidade_disponivel');

                if ($quantidadeEmEstoque < $item['quantidade']) {
                    $itensForaEstoque[] = [
                        'produto_id' => $item['produto_id'],
                        'quantidade_solicitada' => $item['quantidade'],
                        'quantidade_disponivel' => $quantidadeEmEstoque
                    ];
                }
                Log::channel('stderr')->info('>> PedidoController verifyItemsInStock item: ' . json_encode($item, JSON_PRETTY_PRINT));
            }

            $todosItensEmEstoque = empty($itensForaEstoque); //retorna true ou false, se for vazio 

            Log::channel('stderr')->info('>> PedidoController verification: ' . json_encode([
                'todos_itens_em_estoque' => $todosItensEmEstoque,
                'itens_fora_estoque' => $itensForaEstoque
            ], JSON_PRETTY_PRINT));

            return [$todosItensEmEstoque, $itensForaEstoque];
        }

        function updateStock($pedidoItensParam, $quantidadeItensParam){
            Log::channel('stderr')->info('>> PedidoController updateStock: atualizando estoque.');

            $localPedidoItensParam = json_decode($pedidoItensParam, true);
            Log::channel('stderr')->info('>> PedidoController updateStock localPedidoItensParam: ' . json_encode($localPedidoItensParam, JSON_PRETTY_PRINT));
            
            foreach ($localPedidoItensParam as $item) {
                $quantidadeItem = $item['quantidade'];
                $quantidadeDisponivel = DB::table('estoque')
                    ->where('id', $item['produto_id'])
                    ->value('quantidade_disponivel');

                DB::transaction(function () use ($item, $quantidadeItem, $quantidadeDisponivel){
                    DB::table('estoque')
                        ->where('id', $item['produto_id'])
                        ->update([
                            'quantidade_disponivel' => $quantidadeDisponivel - $quantidadeItem
                        ]);
                });
            }

            return true;
        }


        function insertPedido($pedidoIdUsuarioParam, $pedidoItensParam, $pedidoTotalValorParam, $pedidoItensQuantityParam, $pedidouuidParam, $pedidoDataCriacaoParam, $pedidoStatusParam){
            Log::channel('stderr')->info('>> PedidoController insertPedido: inserindo pedido na base de dados.');

            

            
            // $pedidoDataCriacao = date('Y-m-d H:i:s');

            $pedidoItensParam = json_encode($pedidoItensParam);
            // $pedidoItensQuantity = $pedidoItensQuantityParam;
            

            DB::transaction(function () use ($pedidoIdUsuarioParam, $pedidoItensParam, $pedidoTotalValorParam, $pedidoStatusParam, $pedidoDataCriacaoParam, $pedidoItensQuantityParam, $pedidouuidParam) {
                
                $pedido = DB::table('pedidos')->insert([
                    'id_usuario' => $pedidoIdUsuarioParam,
                    'data_pedido' => $pedidoDataCriacaoParam,
                    'status' => $pedidoStatusParam[0],
                    'total_valor' => $pedidoTotalValorParam,
                    'itens_pedido' => $pedidoItensParam,
                    'id' => $pedidouuidParam,
                ]);

                Log::channel('stderr')->info('>> PedidoController insertPedido: pedido criado com sucesso.');
                Log::channel('stderr')->info('>> PedidoController insertPedido: atualizando estoque.');
                
                $updateStock = updateStock($pedidoItensParam, $pedidoItensQuantityParam);

                if(!$updateStock){
                    Log::channel('stderr')->info('>> PedidoController insertPedido: erro ao atualizar estoque.');

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Erro ao atualizar estoque',
                    ], 500);
                }
            });

            return true;
        }

        $pedidoItensOrganized = organizeItems($pedidoItens);
        if(!$pedidoItensOrganized){
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao organizar itens do pedido',
            ], 500);
        }

        $pedidoItensTotal = calculateItemsTotal($pedidoItensOrganized);
        if(!$pedidoItensTotal){
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao calcular total dos itens do pedido',
            ], 500);
        }

        $pedidoItensQuantity = returnItemsAndQuantities($pedidoItensOrganized);
        if(!$pedidoItensQuantity){
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao retornar quantidade dos itens do pedido',
            ], 500);
        }

        $pedidoItensInStock = verifyItemsInStock($pedidoItensOrganized, $pedidoItensQuantity);
        if(!$pedidoItensInStock[0]){
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao verificar se itens estão em estoque',
                'details' => $pedidoItensInStock[1]
            ], 403);
        }

        $pedidouuid = Str::uuid();
        $pedidoDataCriacao = date('Y-m-d H:i:s');
        $pedidoStatus = ["pendente", "processando", "enviado", "entregue", "cancelado"];
        $insertPedido = insertPedido($pedidoIdUsuario, $pedidoItensOrganized, $pedidoItensTotal, $pedidoItensQuantity, $pedidouuid, $pedidoDataCriacao, $pedidoStatus);
        if(!$insertPedido){
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao inserir pedido na base de dados',
            ], 500);
        }
        

        return response()->json([
            'status' => 'success',
            'message' => 'Pedido criado com sucesso',
            'pedido_id' => $pedidouuid,
            'pedido_data_criacao' => $pedidoDataCriacao,
            'pedido_status' => $pedidoStatus
        ], 201);
    }

    public function listItens(Request $request){
        Log::channel('stderr')->info('>> Requisição recebida em PedidoController.listItens. ');
        Log::channel('stderr')->info('>> PedidoController.listItens recebido: ' . json_encode($request->all()));

        $query = "SELECT * FROM estoque";
        $result = DB::table('estoque')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Pedido itens listados com sucesso',
            'itens' => json_encode($result),
        ]);
    }
}

