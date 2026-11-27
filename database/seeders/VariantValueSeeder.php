<?php

namespace Database\Seeders;

use App\Models\VariantValue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VariantValue::create([
            'id' => 1,
            'variant_id' => 1,
            'value' => '500 gram'
        ]);
        VariantValue::create([
            'id' => 2,
            'variant_id' => 1,
            'value' => '1 kg'
        ]);
        VariantValue::create([
            'id' => 3,
            'variant_id' => 2,
            'value' => 'Standar Segar'
        ]);
        VariantValue::create([
            'id' => 4,
            'variant_id' => 2,
            'value' => 'Organik Super'
        ]);
    }
}
