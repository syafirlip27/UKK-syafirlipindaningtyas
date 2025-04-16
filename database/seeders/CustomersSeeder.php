<?php

namespace Database\Seeders;

use App\Models\Customers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
   /**
     * Run the database seeds.
     */
     public function run(): void
    {
        //
        {
            $users = [
                [
                    'name' => 'firly pinda',
                    'no_hp' => '083811628582',
                    'point' => '100'
                ],
                [
                    'name' => 'feren deswita',
                    'no_hp' => '083811628990',
                    'point' => '100',
                ],
            ];
    
            foreach ($users as $user) {
                Customers::create($user);
            }
        }
    }
}
