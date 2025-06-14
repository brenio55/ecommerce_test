<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



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
        $pedidoTotalValor = $request->input('pedido_total_valor');
        $pedidoItens = $request->input('pedido_itens');

        $pedidoItens = json_decode($pedidoItens, true);

        // $pedidoItens = collect($pedidoItens)->map(function($item){
        //     return [
        //         'produto_id' => $item['produto_id'],
        //         'quantidade' => $item['quantidade'],
        //         'valor_unitario_atTheTime' => $item['valor_unitario_atTheTime'],
        //     ];
        // });
        Log::channel('stderr')->info('>> PedidoController pedidoItens: ' . json_encode($pedidoItens));

        $pedidouuid = "gerado automaticamente";
        $pedidoDataCriacao = date('Y-m-d H:i:s');
        $pedidoStatus = "gerado automaticamente";

        function insertPedido($pedidoIdUsuario, $pedidoTotalValor, $pedidoItens){
            
        }
        

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

