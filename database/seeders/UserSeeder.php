<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
       User::create([
            'name' => 'MASA Concretos',
            'email' => 'subdireccion@masaconcretos.com.mx',
            'password' => Hash::make('mp-ws-masaconcretos-1234'),
        ]);

        User::create([
            'name' => 'GPS RASTREO POR SATELITE',
            'email' => 'alejandromr@rastreoporsatelite.com.mx',
            'password' => Hash::make('mp-ws@1234'),
        ]);

    }
}
