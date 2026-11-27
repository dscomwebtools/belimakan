<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('setting_address')->insert([
            'name'       => 'BeliMakan Fresh Market',
            'hp'         => '6281234567890',
            'email'      => 'halo@belimakan.id',
            'address'    => 'Sentra Distribusi Sayur Segar BeliMakan, Jl. Raya Pasar Minggu No. 45',
            'area_id'    => 'IDNP6IDNC148IDND839IDZ12520',
            'area_name'  => 'Pasar Minggu, Jakarta Selatan, DKI Jakarta. 12520',
            'postal_code' => '12520',
            'latitude'   => -6.284167,
            'longitude'  => 106.843611,
            'province'   => 'DKI Jakarta',
            'city'       => 'Jakarta Selatan',
            'district'   => 'Pasar Minggu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
