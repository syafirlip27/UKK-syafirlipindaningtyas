<?php

namespace Database\Seeders;

use App\Models\Detail_sales;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailSalesSeeder extends Seeder
{
   /**
     * Run the database seeds.
     */
     public function run(): void
    {
        {
            $users = [
                [
                    'sale_id' => '1',
                    'product_id' => '1',
                    'amount' => '2',
                    'subtotal' => '40000'
                ],
                [
                    'sale_id' => '2',
                    'product_id' => '1',
                    'amount' => '2',
                    'subtotal' => '50000'
                ],
            ];
    
            foreach ($users as $user) {
                Detail_sales::create($user);
            }
        }
    }
}
