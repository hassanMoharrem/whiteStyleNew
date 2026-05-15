<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'White Style', 'image' => 'images/brands/white-style-logo.png'],
            ['name' => 'Nike', 'image' => 'images/brands/Nike.png'],
            ['name' => 'Adidas', 'image' => 'images/brands/Adidas.png'],
            ['name' => 'BOSS', 'image' => 'images/brands/BOSS.png'],
            ['name' => 'DIESEL', 'image' => 'images/brands/DIESEL.png'],
            ['name' => 'EMPORIO ARMANI', 'image' => 'images/brands/EMPORIOARMANI.png'],
            ['name' => 'GUCCI', 'image' => 'images/brands/GUCCI.png'],
            ['name' => 'LACOSTE', 'image' => 'images/brands/LACOSTE.png'],
            ['name' => 'POLO', 'image' => 'images/brands/POLO.png'],
            ['name' => 'PRADA', 'image' => 'images/brands/PRADA.png'],
            ['name' => 'UNDER ARMOUR', 'image' => 'images/brands/UNDERARMOUR.png'],

        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
