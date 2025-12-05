<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fasilitas;
use App\Models\TipeKamar;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat Daftar Semua Fasilitas
        // Fasilitas Dasar (Gratis / Include Kamar)
        $wifi = Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'WiFi'],
            ['biaya_tambahan' => 0, 'icon' => 'fa-wifi', 'deskripsi' => 'Koneksi internet kecepatan tinggi.']
        );

        $ac = Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'AC'],
            ['biaya_tambahan' => 0, 'icon' => 'fa-snowflake', 'deskripsi' => 'Penyejuk ruangan.']
        );

        $tv = Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'TV'],
            ['biaya_tambahan' => 0, 'icon' => 'fa-tv', 'deskripsi' => 'Televisi layar datar dengan saluran kabel.']
        );

        $bathtub = Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'Bathtub'],
            ['biaya_tambahan' => 0, 'icon' => 'fa-bath', 'deskripsi' => 'Bak mandi rendam pribadi.']
        );

        $balkon = Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'Balkon'],
            ['biaya_tambahan' => 0, 'icon' => 'fa-door-open', 'deskripsi' => 'Teras pribadi dengan pemandangan.']
        );

        $ruangKeluarga = Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'Ruang Keluarga'],
            ['biaya_tambahan' => 0, 'icon' => 'fa-couch', 'deskripsi' => 'Area duduk terpisah yang luas.']
        );

        // Tambahan (Berbayar)
        // Fasilitas ini bisa dipilih user saat booking
        Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'Layanan Spa'],
            [
                'biaya_tambahan' => 150000.00, // Contoh harga
                'icon' => 'fa-spa',
                'deskripsi' => 'Pijat relaksasi durasi 60 menit.'
            ]
        );

        Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'Akses Kolam Renang'],
            [
                'biaya_tambahan' => 50000.00,
                'icon' => 'fa-swimming-pool',
                'deskripsi' => 'Akses harian ke kolam renang infinity.'
            ]
        );

        Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'Akses Gym'],
            [
                'biaya_tambahan' => 35000.00,
                'icon' => 'fa-dumbbell',
                'deskripsi' => 'Akses ke pusat kebugaran lengkap.'
            ]
        );

        // Tambahan Extra Bed jika diperlukan di masa depan
        Fasilitas::firstOrCreate(
            ['nama_fasilitas' => 'Extra Bed'],
            ['biaya_tambahan' => 100000.00, 'icon' => 'fa-bed', 'deskripsi' => 'Kasur tambahan untuk 1 orang.']
        );

        // MEHUBUNGKAN FASILITAS KE TIPE KAMAR

        // Mengambil data Tipe Kamar
        $standard = TipeKamar::where('nama_tipe_kamar', 'Standard')->first();
        $deluxe = TipeKamar::where('nama_tipe_kamar', 'Deluxe')->first();
        $suite = TipeKamar::where('nama_tipe_kamar', 'Suite')->first();
        $family = TipeKamar::where('nama_tipe_kamar', 'Family Room')->first();

        // Kamar Standard: WiFi, AC
        if ($standard) {
            $standard->fasilitas()->sync([
                $wifi->id_fasilitas,
                $ac->id_fasilitas,
            ]);
        }

        // Kamar Deluxe: WiFi, AC, TV
        if ($deluxe) {
            $deluxe->fasilitas()->sync([
                $wifi->id_fasilitas,
                $ac->id_fasilitas,
                $tv->id_fasilitas,
            ]);
        }

        // Kamar Suite: WiFi, AC, Bathtub, Balkon
        if ($suite) {
            $suite->fasilitas()->sync([
                $wifi->id_fasilitas,
                $ac->id_fasilitas,
                $bathtub->id_fasilitas,
                $balkon->id_fasilitas,
            ]);
        }

        // Family Room: WiFi, AC, TV, Bathtub, Balkon, Ruang Keluarga
        if ($family) {
            $family->fasilitas()->sync([
                $wifi->id_fasilitas,
                $ac->id_fasilitas,
                $tv->id_fasilitas,
                $bathtub->id_fasilitas,
                $balkon->id_fasilitas,
                $ruangKeluarga->id_fasilitas,
            ]);
        }

        $this->command->info('Data fasilitas dan relasinya berhasil di-seed!');
    }
}
