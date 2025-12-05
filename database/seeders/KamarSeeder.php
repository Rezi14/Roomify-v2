<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kamar;     
use App\Models\TipeKamar;

class KamarSeeder extends Seeder
{
    // Run the database seeds.
    public function run(): void
    {
        $standardTipe = TipeKamar::where('nama_tipe_kamar', 'Standard')->first();
        $deluxeTipe = TipeKamar::where('nama_tipe_kamar', 'Deluxe')->first();
        $suiteTipe = TipeKamar::where('nama_tipe_kamar', 'Suite')->first();
        $familyTipe = TipeKamar::where('nama_tipe_kamar', 'Family Room')->first();

        // Jika salah satu tipe kamar tidak ditemukan, berikan pesan error
        if (!$standardTipe || !$deluxeTipe || !$suiteTipe || !$familyTipe) {
            $this->command->error('Pastikan TipeKamarSeeder sudah dijalankan dan data tipe kamar tersedia!');
            return;
        }

        $kamars = [
            [
                'id_tipe_kamar' => $standardTipe->id_tipe_kamar, // Menggunakan id_tipe_kamar sesuai primary key di TipeKamar
                'nomor_kamar' => '101',
                'status_kamar' => true,
            ],
            [
                'id_tipe_kamar' => $standardTipe->id_tipe_kamar,
                'nomor_kamar' => '102',
                'status_kamar' => true,
            ],
            [
                'id_tipe_kamar' => $standardTipe->id_tipe_kamar,
                'nomor_kamar' => '103',
                'status_kamar' => true,
            ],

            // Kamar Deluxe
            [
                'id_tipe_kamar' => $deluxeTipe->id_tipe_kamar,
                'nomor_kamar' => '201',
                'status_kamar' => true,
            ],
            [
                'id_tipe_kamar' => $deluxeTipe->id_tipe_kamar,
                'nomor_kamar' => '202',
                'status_kamar' => true,
            ],
            [
                'id_tipe_kamar' => $deluxeTipe->id_tipe_kamar,
                'nomor_kamar' => '203',
                'status_kamar' => true,
            ],

            [
                'id_tipe_kamar' => $suiteTipe->id_tipe_kamar,
                'nomor_kamar' => '301',
                'status_kamar' => true,
            ],
            [
                'id_tipe_kamar' => $suiteTipe->id_tipe_kamar,
                'nomor_kamar' => '302',
                'status_kamar' => true,
            ],

            // Family Room
            [
                'id_tipe_kamar' => $familyTipe->id_tipe_kamar,
                'nomor_kamar' => '401',
                'status_kamar' => true,
            ],
        ];

        foreach ($kamars as $kamar) {
            Kamar::updateOrCreate(
                ['nomor_kamar' => $kamar['nomor_kamar']],
                $kamar
            );
        }

        $this->command->info('Kamar telah berhasil di-seed!');
    }
}
