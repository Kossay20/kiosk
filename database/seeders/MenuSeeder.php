<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            ['name' => 'Burger Beef', 'description' => 'Burger daging sapi juicy', 'price' => 35000, 'category' => 'Food'],
            ['name' => 'Fried Chicken', 'description' => 'Ayam goreng crispy', 'price' => 25000, 'category' => 'Food'],
            ['name' => 'French Fries', 'description' => 'Kentang goreng renyah', 'price' => 15000, 'category' => 'Snack'],
            ['name' => 'Coca Cola', 'description' => 'Minuman bersoda dingin', 'price' => 10000, 'category' => 'Drink'],
            ['name' => 'Ice Coffee', 'description' => 'Kopi dingin segar', 'price' => 20000, 'category' => 'Drink'],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
