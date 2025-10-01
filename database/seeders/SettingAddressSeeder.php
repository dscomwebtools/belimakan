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
            'name'       => 'DS Computer',
            'hp'         => '6281383655055',
            'email'      => 'dscomputerwebtools@gmail.com',
            'address'    => 'test',
            'area_id'    => 'IDNP6IDNC147IDND833IDZ10730',
            'area_name'  => 'Sawah Besar, Jakarta Pusat, DKI Jakarta. 10730',
            'postal_code' => '10730',
            'province'   => 'DKI Jakarta',
            'city'       => 'Jakarta Pusat',
            'district'   => 'Sawah Besar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
