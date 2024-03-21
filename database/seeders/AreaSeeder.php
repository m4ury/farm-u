<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
['nombre_area' => 'botiquín urgencias'],
['nombre_area' => 'carro de paro urgencias'],
['nombre_area' => 'maletín urgencias'],
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate($area);
        }
    }
}
