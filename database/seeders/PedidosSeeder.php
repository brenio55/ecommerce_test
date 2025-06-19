<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pedidos')->insert([
            [
                'id_usuario' => 1,
                'data_pedido' => date('Y-m-d H:i:s'),
                'status' => 'novo',
                'total_valor' => 80.00,
                'itens_pedido' => json_encode([
                    [
                        "produto_id" => 1,
                        "quantidade" => 2,
                        "valor_unitario_atTheTime" => 15
                    ],
                    [
                        "produto_id" => 2,
                        "quantidade" => 5,
                        "valor_unitario_atTheTime" => 10
                    ]
                ]),
            ],
            [
                'id_usuario' => 2,
                'data_pedido' => date('Y-m-d H:i:s'),
                'status' => 'processando',
                'total_valor' => 45.00,
                'itens_pedido' => json_encode([
                    [
                        "produto_id" => 3,
                        "quantidade" => 3,
                        "valor_unitario_atTheTime" => 15
                    ]
                ]),
            ],
        ]);
    }
}
