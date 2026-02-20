<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Pcs',    'abbreviation' => 'pcs'],
            ['name' => 'Kg',     'abbreviation' => 'kg'],
            ['name' => 'Gram',   'abbreviation' => 'gr'],
            ['name' => 'Liter',  'abbreviation' => 'L'],
            ['name' => 'Meter',  'abbreviation' => 'm'],
            ['name' => 'Roll',   'abbreviation' => 'rl'],
            ['name' => 'Box',    'abbreviation' => 'box'],
            ['name' => 'Pack',   'abbreviation' => 'pk'],
            ['name' => 'Sak',    'abbreviation' => 'sak'],
            ['name' => 'Kubik',  'abbreviation' => 'm³'],
            ['name' => 'Lonjor', 'abbreviation' => 'lbr'],
            ['name' => 'Lembar', 'abbreviation' => 'lbr'],
            ['name' => 'Batang', 'abbreviation' => 'btg'],
            ['name' => 'Buah',   'abbreviation' => 'bh'],
            ['name' => 'Set',    'abbreviation' => 'set'],
            ['name' => 'Pasang', 'abbreviation' => 'ps'],
            ['name' => 'Dus',    'abbreviation' => 'dus'],
            ['name' => 'Kaleng', 'abbreviation' => 'klg'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit['name']], $unit);
        }
    }
}
