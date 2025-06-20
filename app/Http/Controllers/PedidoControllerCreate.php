<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\map;
use Illuminate\Support\Str;
use App\Models\PedidoModel;


class PedidoControllerCreate extends Controller
{

    public function create(Request $request){
        Log::channel('stderr')->info('>> Requisição recebida em PedidoController. ');
        Log::channel('stderr')->info('>> PedidoController recebido: ' . json_encode($request->all()));


        $pedidoIdUsuario = $request->input('pedido_id_usuario');
        $pedidoItens = $request->input('pedido_itens');
        $pedidoItens = json_decode($pedidoItens, true);

        function organizeItems($pedidoItensParam){

            $localPedidoItensParam = $pedidoItensParam;
            Log::channel('stderr')->info('>> PedidoController pedidoItens decoded: ' . json_encode($localPedidoItensParam, JSON_PRETTY_PRINT));

            $pedidoItensMapped = collect($localPedidoItensParam)->map(function($item) {
                return [
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'valor_unitario_atTheTime' => $item['valor_unitario_atTheTime'],
                ];
            })->toArray();

            Log::channel('stderr')->info('>> PedidoController pedidoItensMapped: ' . json_encode($pedidoItensMapped, JSON_PRETTY_PRINT));
            $itensOrganized = $pedidoItensMapped;

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


        function insertPedido($pedidoIdUsuarioParam, $pedidoItensParam, $pedidoTotalValorParam, $pedidoItensQuantityParam, $pedidouuidParam, $pedidoDataCriacaoParam, $pedidoStatusCodesParam){
            Log::channel('stderr')->info('>> PedidoController insertPedido: inserindo pedido na base de dados.');

            $pedidoItensParam = json_encode($pedidoItensParam);

            DB::transaction(function () use ($pedidoIdUsuarioParam, $pedidoItensParam, $pedidoTotalValorParam, $pedidoStatusCodesParam, $pedidoDataCriacaoParam, $pedidoItensQuantityParam, $pedidouuidParam) {
                
                $pedido = DB::table('pedidos')->insert([
                    'id_usuario' => $pedidoIdUsuarioParam,
                    'data_pedido' => $pedidoDataCriacaoParam,
                    'status' => $pedidoStatusCodesParam['pendente'],
                    'total_valor' => $pedidoTotalValorParam,
                    'itens_pedido' => $pedidoItensParam,
                    'id' => $pedidouuidParam,
                ]);

                if (!$pedido){
                    Log::channel('stderr')->info('>> PedidoController insertPedido: erro ao inserir pedido na base de dados.');
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Erro ao inserir pedido na base de dados',
                    ], 500);
                }

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
        $pedidoStatusCodes = PedidoModel::STATUS_TYPES;
        
        $insertPedido = insertPedido($pedidoIdUsuario, $pedidoItensOrganized, $pedidoItensTotal, $pedidoItensQuantity, $pedidouuid, $pedidoDataCriacao, $pedidoStatusCodes);
        if(!$insertPedido){
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao inserir pedido na base de dados',
            ], 500);
        }
        

        return response()->json([
            'status' => 'success',
            'message' => 'Pedido criado com sucesso',
            'data' => [ 
                'pedido_id' => $pedidouuid,
                'pedido_data_criacao' => $pedidoDataCriacao,
                'pedido_status' => $pedidoStatusCodes['pendente']
            ]
        ], 201);
    }
}

