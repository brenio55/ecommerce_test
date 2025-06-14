<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\map;



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
        Log::channel('stderr')->info('>> .env SUPABASE_URL: ' . env('SUPABASE_URL'));
        Log::channel('stderr')->info('>> .env SUPABASE_ANON_KEY: ' . env('SUPABASE_ANON_KEY'));

        Log::channel('stderr')->info('>> Requisição recebida em PedidoController. ');
        Log::channel('stderr')->info('>> PedidoController recebido: ' . json_encode($request->all()));


        $pedidoIdUsuario = $request->input('pedido_id_usuario');
        $pedidoItens = $request->input('pedido_itens');
        $pedidoItens = json_decode($pedidoItens, true);

        $pedidouuid = "gerado automaticamente";
        $pedidoTotalValor = "gerado automaticamente";
        $pedidoStatus = "gerado automaticamente";
        $pedidoDataCriacao = date('Y-m-d H:i:s');

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

        function calculateItemsTotal($pedidoItensParam){
            Log::channel('stderr')->info('>> PedidoController calculateItemsTotal: calculando total dos itens do pedido.');

            $localPedidoItensParam = $pedidoItensParam;

            $itemsTotal = collect($localPedidoItensParam)->map(function($item) {
                return $item['quantidade'] * $item['valor_unitario_atTheTime'];
            })->sum();
            

            Log::channel('stderr')->info('>> PedidoController itemsTotal: ' . $itemsTotal);

            return $itemsTotal;
        }

        function verifyItemsInStock($pedidoItensParam){
            Log::channel('stderr')->info('>> PedidoController verifyItemsInStock: verificando se item tem em estoque.');

            $localPedidoItensParam = $pedidoItensParam;

            return 0;
        }


        function insertPedido($pedidoIdUsuarioParam, $pedidoItensParam){
            Log::channel('stderr')->info('>> PedidoController insertPedido: inserindo pedido na base de dados.');

            // $query = "SELECT * FROM estoque WHERE id = $pedidoItensParam[produto_id]";
            // $result = DB::table('estoque')->where('id', $pedidoItensParam['produto_id'])->first();



            // if($result){
            //     Log::channel('stderr')->info('>> PedidoController insertPedido: item em estoque.');
            // }

            // $result = DB::table('pedidos')->insert($query);


            return 0;
        }

        $pedidoItensOrganized = organizeItems($pedidoItens);
        $pedidoItensTotal = calculateItemsTotal($pedidoItensOrganized);
        // $pedidoItensInStock = verifyItemsInStock($pedidoItensOrganized);
        insertPedido($pedidoIdUsuario, $pedidoItensOrganized);
        

        return response()->json([
            'status' => 'success',
            'message' => 'Pedido criado com sucesso',
            'pedido_id' => $pedidouuid,
            'pedido_data_criacao' => $pedidoDataCriacao,
            'pedido_status' => $pedidoStatus
        ]);
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

