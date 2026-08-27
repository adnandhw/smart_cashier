<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Categories
        $categoriesData = [
            [
                'name' => 'Kue Basah & Tradisional',
                'icon' => 'Flame', // Lucide icon names
                'color' => 'emerald',
            ],
            [
                'name' => 'Roti & Pastry',
                'icon' => 'Croissant',
                'color' => 'amber',
            ],
            [
                'name' => 'Gorengan & Asin',
                'icon' => 'Soup',
                'color' => 'orange',
            ],
            [
                'name' => 'Dessert & Western',
                'icon' => 'Cake',
                'color' => 'pink',
            ],
            [
                'name' => 'Minuman & Lainnya',
                'icon' => 'Coffee',
                'color' => 'blue',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
                'color' => $cat['color'],
            ]);
        }

        // 2. Define Products matching YOLOv8 Classes
        // YOLO Classes: arem arem, bacang, donut cokelat, ekler, horn fla, iceberg cheese cake, 
        // ketan srikaya, lapis pepe cokelat, lapis pepe pandan, lemper, macaroni schotel, 
        // nagasari, pai buah, pastel, pisang molen, risol mayonese, risol segitiga, soes, 
        // sosis pastry, sosis solo, tahu baso
        $products = [
            [
                'name' => 'Arem Arem Daging',
                'coco_class' => 'arem arem',
                'price' => 3500.00,
                'stock' => 45,
                'category' => 'Kue Basah & Tradisional',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Bacang Ayam',
                'coco_class' => 'bacang',
                'price' => 6000.00,
                'stock' => 30,
                'category' => 'Kue Basah & Tradisional',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Donut Cokelat Meses',
                'coco_class' => 'donut cokelat',
                'price' => 4500.00,
                'stock' => 50,
                'category' => 'Roti & Pastry',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Ekler Vla Cokelat',
                'coco_class' => 'ekler',
                'price' => 5500.00,
                'stock' => 25,
                'category' => 'Roti & Pastry',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Horn Fla Vanilla',
                'coco_class' => 'horn fla',
                'price' => 6000.00,
                'stock' => 20,
                'category' => 'Roti & Pastry',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Iceberg Cheese Cake',
                'coco_class' => 'iceberg cheese cake',
                'price' => 15000.00,
                'stock' => 15,
                'category' => 'Dessert & Western',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Ketan Srikaya Pandan',
                'coco_class' => 'ketan srikaya',
                'price' => 4000.00,
                'stock' => 40,
                'category' => 'Kue Basah & Tradisional',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Lapis Pepe Cokelat',
                'coco_class' => 'lapis pepe cokelat',
                'price' => 3500.00,
                'stock' => 45,
                'category' => 'Kue Basah & Tradisional',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Lapis Pepe Pandan Suji',
                'coco_class' => 'lapis pepe pandan',
                'price' => 3500.00,
                'stock' => 45,
                'category' => 'Kue Basah & Tradisional',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Lemper Ayam Spesial',
                'coco_class' => 'lemper',
                'price' => 4000.00,
                'stock' => 60,
                'category' => 'Kue Basah & Tradisional',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Macaroni Schotel Mini',
                'coco_class' => 'macaroni schotel',
                'price' => 8500.00,
                'stock' => 8, // Set low stock (under 10) to test alerting
                'category' => 'Dessert & Western',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Nagasari Pisang Raja',
                'coco_class' => 'nagasari',
                'price' => 3000.00,
                'stock' => 35,
                'category' => 'Kue Basah & Tradisional',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Pai Buah Segar',
                'coco_class' => 'pai buah',
                'price' => 5000.00,
                'stock' => 25,
                'category' => 'Roti & Pastry',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Pastel Sayur & Telur',
                'coco_class' => 'pastel',
                'price' => 4000.00,
                'stock' => 50,
                'category' => 'Gorengan & Asin',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Pisang Molen Renyah',
                'coco_class' => 'pisang molen',
                'price' => 3000.00,
                'stock' => 4, // Set low stock to test alerts
                'category' => 'Gorengan & Asin',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Risol Mayonese Premium',
                'coco_class' => 'risol mayonese',
                'price' => 4500.00,
                'stock' => 40,
                'category' => 'Gorengan & Asin',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Risol Segitiga Ragout',
                'coco_class' => 'risol segitiga',
                'price' => 4000.00,
                'stock' => 35,
                'category' => 'Gorengan & Asin',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Soes Fla Vanilla',
                'coco_class' => 'soes',
                'price' => 4500.00,
                'stock' => 30,
                'category' => 'Roti & Pastry',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Sosis Pastry Roll',
                'coco_class' => 'sosis pastry',
                'price' => 7000.00,
                'stock' => 15,
                'category' => 'Roti & Pastry',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Sosis Solo Goreng',
                'coco_class' => 'sosis solo',
                'price' => 4000.00,
                'stock' => 50,
                'category' => 'Gorengan & Asin',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Tahu Baso Semarang',
                'coco_class' => 'tahu baso',
                'price' => 5000.00,
                'stock' => 20,
                'category' => 'Gorengan & Asin',
                'image_url' => 'logo.png',
            ],
            [
                'name' => 'Air Mineral Botol 600ml',
                'coco_class' => 'bottle', // Keep this as general bottle just in case
                'price' => 4000.00,
                'stock' => 100,
                'category' => 'Minuman & Lainnya',
                'image_url' => 'logo.png',
            ]
        ];

        foreach ($products as $p) {
            $catName = $p['category'];
            $catId = isset($categories[$catName]) ? $categories[$catName]->id : null;

            Product::create([
                'name' => $p['name'],
                'price' => $p['price'],
                'coco_class' => $p['coco_class'],
                'stock' => $p['stock'],
                'category_id' => $catId,
                'image_url' => $p['image_url'],
            ]);
        }
    }
}
