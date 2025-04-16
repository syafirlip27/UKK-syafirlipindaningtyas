<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        {
            $users = [
                [
                    'name' => 'Mixer Kue',
                    'price' => '20000',
                    'stock' => '10',
                    'image' => 'image'
                ],
                [
                    'name' => 'Spatula',
                    'price' => '25000',
                    'stock' => '10',
                    'image' => 'image'
                ],
            ];
    
            foreach ($users as $user) {
                Products::create($user);
            }
        }
    }
}
