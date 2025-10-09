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
            1 => "Menunggu Konfirmasi",
            2 => "Menunggu Pembayaran",
            3 => "Menunggu Verifikasi Pembayaran",
            4 => "Diproses",
            5 => "Dikirim",
            6 => "Selesai",
            7 => "Dibatalkan",
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

        // --- STATUS DARI BITESHIP (dengan label user-friendly) ---
        $startId = 8;
        $biteshipStatuses = [
            ['biteship_status' => 'confirmed',         'biteship_status_name' => 'Order Terkonfirmasi',    'description' => 'Order sudah terkonfirmasi, siap diproses.'],
            ['biteship_status' => 'scheduled',         'biteship_status_name' => 'Pengiriman Dijadwalkan', 'description' => 'Sudah dijadwalkan untuk dikirim.'],
            ['biteship_status' => 'allocated',         'biteship_status_name' => 'Kurir Dialokasikan',     'description' => 'Kurir sudah ditugaskan untuk pickup.'],
            ['biteship_status' => 'picking_up',        'biteship_status_name' => 'Kurir Menuju Lokasi Pickup', 'description' => 'Kurir sedang menuju lokasi pengambilan paket.'],
            ['biteship_status' => 'picked',            'biteship_status_name' => 'Paket Sudah Diambil Kurir', 'description' => 'Paket berhasil dijemput kurir.'],
            ['biteship_status' => 'cancelled',         'biteship_status_name' => 'Order Dibatalkan',       'description' => 'Order dibatalkan.'],
            ['biteship_status' => 'on_hold',           'biteship_status_name' => 'Order Ditahan',          'description' => 'Pengiriman ditunda untuk alasan tertentu.'],
            ['biteship_status' => 'dropping_off',      'biteship_status_name' => 'Paket Sedang Dikirim',         'description' => 'Paket sedang dalam perjalanan ke penerima.'],
            ['biteship_status' => 'return_in_transit', 'biteship_status_name' => 'Paket Dalam Proses Retur', 'description' => 'Paket sedang dalam perjalanan kembali ke pengirim.'],
            ['biteship_status' => 'returned',          'biteship_status_name' => 'Paket Sudah Dikembalikan', 'description' => 'Paket berhasil dikembalikan ke pengirim.'],
            ['biteship_status' => 'rejected',          'biteship_status_name' => 'Paket Ditolak',          'description' => 'Penerima menolak paket.'],
            ['biteship_status' => 'disposed',          'biteship_status_name' => 'Paket Dihancurkan',      'description' => 'Paket dimusnahkan.'],
            ['biteship_status' => 'courier_not_found', 'biteship_status_name' => 'Kurir Tidak Ditemukan',  'description' => 'Sistem gagal menemukan kurir untuk order ini.'],
            ['biteship_status' => 'delivered',         'biteship_status_name' => 'Paket Terkirim',         'description' => 'Paket sudah sampai ke penerima.'],
        ];

        foreach ($biteshipStatuses as $index => $status) {
            DeliveryState::updateOrCreate(
                ['biteship_status' => $status['biteship_status']],
                [
                    'id' => $startId + $index,
                    'name' => $status['biteship_status_name'],
                    'biteship_status_name' => $status['biteship_status_name'],
                    'description' => $status['description'],
                    'is_biteship_status' => true,
                ]
            );
        }
    }
}
