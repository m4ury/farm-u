<?php

namespace Database\Seeders;

use App\Models\Lote;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lotes = [
            ['farmaco_id' =>	1,	'num_serie' =>	'008111rt',	'fecha_vencimiento' =>	'2024-12-31',	'cantidad' =>	 10 	,'cantidad_disponible' =>	 null, 'vencido' => false ],
        ];

        foreach ($lotes as $lote) {
            Lote::create($lote);
        }
    }
}
