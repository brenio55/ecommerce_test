<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstoqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('estoque')->insert([
            [
                'item_nome' => 'Pendrive Ultra Mega Blaster 16GB',
                'quantidade_disponivel' => 10,
            ],
            [
                'item_nome' => 'Mouse Gamer Ultra Mega Blaster 16GB',
                'quantidade_disponivel' => 20,
            ],
            [
                'item_nome' => 'TV 4K Ultra HD Quality Doubly 45"',
                'quantidade_disponivel' => 15,
            ],
        ]);
    }
}
