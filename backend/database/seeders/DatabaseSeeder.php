<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * DatabaseSeeder — Mengisi database dengan data awal (seed).
 *
 * Jalankan dengan: php artisan db:seed
 *
 * Data yang dibuat:
 * - 4 kategori  : Sneakers, Running, Formal, Sandals
 * - 4 brand     : Nike, Adidas, Puma, New Balance
 * - 8 produk    : Masing-masing dengan 1 gambar placeholder dan 7 varian ukuran (38-44)
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // Seed Kategori Produk
        // ============================================================
        $categories = [
            ['name' => 'Sneakers', 'description' => 'Casual and sporty sneakers'],
            ['name' => 'Running', 'description' => 'Performance running shoes'],
            ['name' => 'Formal', 'description' => 'Elegant formal shoes'],
            ['name' => 'Sandals', 'description' => 'Comfortable sandals'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // ============================================================
        // Seed Brand / Merek
        // ============================================================
        $brands = [
            ['name' => 'Nike', 'description' => 'Just Do It'],
            ['name' => 'Adidas', 'description' => 'Impossible Is Nothing'],
            ['name' => 'Puma', 'description' => 'Forever Faster'],
            ['name' => 'New Balance', 'description' => 'Fearlessly Independent'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }

        // ============================================================
        // Seed Produk Sepatu
        // ============================================================
        $products = [
            ['name' => 'Air Max 90', 'price' => 1500000, 'category_id' => 1, 'brand_id' => 1, 'description' => 'Classic Nike Air Max 90 with visible Air cushioning.'],
            ['name' => 'Ultraboost 22', 'price' => 2800000, 'category_id' => 2, 'brand_id' => 2, 'description' => 'Premium running shoe with Boost midsole technology.'],
            ['name' => 'RS-X Reinvention', 'price' => 1200000, 'category_id' => 1, 'brand_id' => 3, 'description' => 'Retro-inspired chunky sneaker with bold colors.'],
            ['name' => 'Fresh Foam 1080', 'price' => 2100000, 'category_id' => 2, 'brand_id' => 4, 'description' => 'Plush cushioning for long-distance comfort.'],
            ['name' => 'Air Force 1 Low', 'price' => 1400000, 'category_id' => 1, 'brand_id' => 1, 'description' => 'Timeless style meets legendary Air cushioning.'],
            ['name' => 'Stan Smith', 'price' => 1100000, 'category_id' => 1, 'brand_id' => 2, 'description' => 'Clean and classic tennis-inspired sneaker.'],
            ['name' => 'Suede Classic', 'price' => 950000, 'category_id' => 1, 'brand_id' => 3, 'description' => 'Iconic streetwear sneaker with suede upper.'],
            ['name' => '574 Core', 'price' => 1300000, 'category_id' => 1, 'brand_id' => 4, 'description' => 'Versatile lifestyle sneaker with ENCAP midsole.'],
        ];

        // Ukuran sepatu yang tersedia untuk setiap produk
        $sizes = ['38', '39', '40', '41', '42', '43', '44'];

        foreach ($products as $productData) {
            // Buat produk dengan slug otomatis dari nama
            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'category_id' => $productData['category_id'],
                'brand_id' => $productData['brand_id'],
            ]);

            // Buat 1 gambar placeholder untuk setiap produk
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => 'products/placeholder.png',
            ]);

            // Buat varian ukuran (38-44) dengan stok random 5-50
            foreach ($sizes as $size) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'stock' => rand(5, 50),
                ]);
            }
        }
    }
}
