<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::truncate();
        User::create([
        'nip'      => '199001012025011001', // Contoh NIP Standar
        'name'     => 'Admin BKPSDM',
        'email'    => 'admin@boyolali.go.id', // Email tetap disimpan utk data kontak
        'password' => Hash::make('password'),
        'gender'   => 'L'
    ]);
    }
}