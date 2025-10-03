<?php

namespace Database\Seeders;

use App\Models\DeliveryState;
use Illuminate\Database\Seeder;

class DeliveryStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- STATUS INTERNAL SISTEM ---
        $states = [
            1 => "Menunggu Pembayaran",
            2 => "Menunggu Konfirmasi",
            3 => "Diproses",
            4 => "Dikirim",
            5 => "Selesai",
            6 => "Dibatalkan",
        ];

        foreach ($states as $id => $name) {
            DeliveryState::updateOrCreate(
                ['id' => $id], // cari berdasarkan ID fix
                [
                    'name' => $name,
                    'is_biteship_status' => false,
                    'biteship_status' => null,
                    'biteship_status_name' => null,
                ]
            );
        }

        // --- STATUS DARI BITESHIP ---
        $biteshipStatuses = [
            ['biteship_status' => 'confirmed',      'biteship_status_name' => 'Terkonfirmasi',       'description' => 'Order is ready to be confirmed. AWB has been generated.'],
            ['biteship_status' => 'scheduled',      'biteship_status_name' => 'Dijadwalkan',        'description' => 'Order has been scheduled to be delivered. AWB has been generated.'],
            ['biteship_status' => 'allocated',      'biteship_status_name' => 'Dialokasikan',       'description' => 'Order has been allocated, courier will pickup the package.'],
            ['biteship_status' => 'picking_up',     'biteship_status_name' => 'Sedang dijemput',    'description' => 'Courier is on the way to pickup the package. (First Mile)'],
            ['biteship_status' => 'picked',         'biteship_status_name' => 'Sudah dijemput',     'description' => 'Package has been picked up by courier.'],
            ['biteship_status' => 'cancelled',      'biteship_status_name' => 'Dibatalkan',         'description' => 'Order has been cancelled.'],
            ['biteship_status' => 'on_hold',        'biteship_status_name' => 'Ditahan',            'description' => 'Order is on hold for any reason.'],
            ['biteship_status' => 'dropping_off',   'biteship_status_name' => 'Sedang dikirim',     'description' => 'Courier is dropping off the package to the receiver. (Last Mile)'],
            ['biteship_status' => 'return_in_transit', 'biteship_status_name' => 'Dalam pengembalian', 'description' => 'Package is on the transit for a return to sender.'],
            ['biteship_status' => 'returned',       'biteship_status_name' => 'Sudah dikembalikan', 'description' => 'Package has been returned to sender.'],
            ['biteship_status' => 'rejected',       'biteship_status_name' => 'Ditolak',            'description' => 'Order has been rejected.'],
            ['biteship_status' => 'disposed',       'biteship_status_name' => 'Dihancurkan',        'description' => 'Package has been disposed / destroyed.'],
            ['biteship_status' => 'courier_not_found', 'biteship_status_name' => 'Kurir tidak ditemukan', 'description' => 'Cannot find courier for the order.'],
            ['biteship_status' => 'delivered',      'biteship_status_name' => 'Terkirim',           'description' => 'Package has been delivered to the receiver.'],
        ];

        foreach ($biteshipStatuses as $status) {
            DeliveryState::updateOrCreate(
                ['biteship_status' => $status['biteship_status']],
                [
                    'name' => $status['biteship_status_name'],
                    'biteship_status_name' => $status['biteship_status_name'],
                    'description' => $status['description'],
                    'is_biteship_status' => true,
                ]
            );
        }
    }
}
