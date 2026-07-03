<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Region;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        // 1. Create the specific regions first
        $regions = [
            ['name' => 'Sindh', 'category' => 'purchase'],
            ['name' => 'Punjab', 'category' => 'purchase'],
            ['name' => 'Tando Allahyar', 'category' => 'sale'],
            ['name' => 'Hyderabad', 'category' => 'sale'],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }

        // // 2. Get the IDs grouped by their category
        $purchaseRegionIds = Region::where('category', 'purchase')->pluck('id')->toArray();
        $saleRegionIds = Region::where('category', 'sale')->pluck('id')->toArray();

        // 3. Create Suppliers and assign a random 'purchase' region
        Supplier::factory(5)->create([
            'region_id' => function () use ($purchaseRegionIds) {
                return collect($purchaseRegionIds)->random();
            },
        ]);

        // 4. Create Customers and assign a random 'sale' region
        Customer::factory(10)->create([
            'region_id' => function () use ($saleRegionIds) {
                return collect($saleRegionIds)->random();
            },
        ]);
        Product::factory(20)->create();
        // Region::factory(1)->create([
        //     'name'=>'sindh'
        // ]);
        // User::factoy(1)->create();
    }
}
