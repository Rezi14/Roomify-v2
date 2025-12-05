<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nama_role', 'admin')->first();
        if (!$adminRole) {
            $this->command->error('Role "admin" tidak ditemukan. Jalankan RoleSeeder terlebih dahulu!');
            return;
        }

        User::firstOrCreate(
            ['email' => 'hoshi1014@gmail.com'],
            [
                'name' => 'rz',
                'password' => Hash::make('123'),
                'id_role' => $adminRole->id_role,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Pengguna Admin telah berhasil di-seed!');
    }
}
