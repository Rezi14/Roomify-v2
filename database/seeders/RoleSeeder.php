<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role; 

class RoleSeeder extends Seeder
{
    // Run the database seeds.
    public function run(): void
    {
        Role::firstOrCreate(
            ['nama_role' => 'admin'],
            ['deskripsi' => 'Pengguna dengan hak akses penuh untuk mengelola sistem.']
        );

        Role::firstOrCreate(
            ['nama_role' => 'pelanggan'],
            ['deskripsi' => 'Pengguna biasa yang dapat memesan kamar.']
        );
    }
}
