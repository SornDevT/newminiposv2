<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the public/products directory exists and is empty before seeding new images
        Storage::disk('public')->deleteDirectory('products');
        Storage::disk('public')->makeDirectory('products');

        $categories = Category::all();
        $units = Unit::all();

        if ($categories->isEmpty() || $units->isEmpty()) {
            $this->command->info('Please run CategorySeeder and UnitSeeder first!');
            return;
        }

        $productNames = [
            'ເຂົ້າຈ້າວ', 'ປາແດກ', 'ຊີ້ນໝູ', 'ໄກ່ສົດ', 'ໄຂ່ໄກ່',
            'ຜັກກາດຂາວ', 'ຜັກບົ່ວ', 'ກະທຽມ', 'ໝາກເຜັດ', 'ໝາກນາວ',
            'ນ້ຳປາ', 'ນ້ຳມັນພືດ', 'ນ້ຳຕານ', 'ເກືອ', 'ແປ້ງນົວ',
            'ກາເຟ', 'ນົມສົດ', 'ນ້ຳດື່ມ', 'ໂຄກ', 'ເບຍລາວ',
            'ໝາກກ້ວຍ', 'ໝາກຫຸ່ງ', 'ໝາກມ່ວງ', 'ໝາກນັດ', 'ໝາກພ້າວ',
            'ເຂົ້າໜົມປັງ', 'ເຂົ້າປຽກ', 'ເຝີ', 'ຕຳໝາກຫຸ່ງ', 'ປີ້ງໄກ່',
            'ສະບູ', 'ຢາສີແຂ້ວ', 'ແປງສີແຂ້ວ', 'ແຊມພູ', 'ຜົງຊັກຟອກ',
            'ເຈ້ຍທິດຊູ', 'ໄມ້ກວາດ', 'ກະຕ່າ', 'ໝໍ້', 'ຈານ',
            'ບິກ', 'ສໍດຳ', 'ຢາງລຶບ', 'ປຶ້ມ', 'ເຈ້ຍ A4',
            'ເຄື່ອງດື່ມຊູກຳລັງ', 'ນ້ຳອັດລົມ', 'ໝາກໄມ້ລວມ', 'ເຂົ້າໜົມຫວານ', 'ນ້ຳໝາກໄມ້'
        ];

        for ($i = 0; $i < 50; $i++) {
            $category = $categories->random();
            $unit = $units->random();
            $productName = $productNames[$i % count($productNames)] . ' ' . ($i + 1); // Ensure unique names

            // Download and store image
            $imageName = Str::uuid() . '.jpg';
            $imageUrl = 'https://picsum.photos/400/400?random=' . rand(1, 1000); // Unique random image

            try {
                $imageContent = file_get_contents($imageUrl);
                Storage::disk('public')->put('products/' . $imageName, $imageContent);
                $imagePath = 'products/' . $imageName;
            } catch (\Exception $e) {
                $this->command->error("Failed to download image for product {$productName}: " . $e->getMessage());
                $imagePath = null; // Fallback if image download fails
            }

            Product::create([
                'name' => $productName,
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'barcode' => 'BAR' . Str::random(7),
                'price' => rand(1000, 100000) / 100 * 100, // Round to nearest 100
                'cost_price' => rand(500, 50000) / 100 * 100,
                'stock_quantity' => rand(1, 200),
                'description' => 'ລາຍລະອຽດຂອງ ' . $productName,
                'status' => 'active',
                'image' => $imagePath,
            ]);
        }

        $this->command->info('ProductSeeder: ' . Product::count() . ' products seeded!');
    }
}
