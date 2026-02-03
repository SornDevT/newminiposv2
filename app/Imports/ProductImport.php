<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class ProductImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    private $failures = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Find or create category
        $category = Category::firstOrCreate(
            ['name' => $row['category']],
            ['slug' => Str::slug($row['category'])]
        );

        // Find or create unit
        $unit = Unit::firstOrCreate(
            ['name' => $row['unit']],
            ['slug' => Str::slug($row['unit'])]
        );

        // Check if a product with the same barcode or name already exists
        $product = Product::where('barcode', $row['barcode'])->first();

        if ($product) {
            // Update existing product
            $product->update([
                'name' => $row['name'],
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'price' => $row['price'],
                'cost_price' => $row['cost_price'] ?? 0,
                'stock_quantity' => $row['stock_quantity'] ?? 0,
                'description' => $row['description'] ?? null,
                'status' => $row['status'] ?? 'active',
            ]);
            return null; // Return null if updating, as ToModel expects new model instances
        } else {
            // Create new product
            return new Product([
                'name' => $row['name'],
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'barcode' => $row['barcode'],
                'price' => $row['price'],
                'cost_price' => $row['cost_price'] ?? 0,
                'stock_quantity' => $row['stock_quantity'] ?? 0,
                'description' => $row['description'] ?? null,
                'status' => $row['status'] ?? 'active',
                'image' => $row['image'] ?? null, // Assuming image path can be in Excel
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'barcode' => 'required|string|max:255|unique:products,barcode',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'image' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'ຊື່ສິນຄ້າ ຕ້ອງມີ.',
            'category.required' => 'ໝວດໝູ່ ຕ້ອງມີ.',
            'unit.required' => 'ຫົວໜ່ວຍ ຕ້ອງມີ.',
            'barcode.required' => 'ບາໂຄດ ຕ້ອງມີ.',
            'barcode.unique' => 'ບາໂຄດນີ້ມີຢູ່ແລ້ວ.',
            'price.required' => 'ລາຄາ ຕ້ອງມີ.',
            'price.numeric' => 'ລາຄາ ຕ້ອງເປັນຕົວເລກ.',
            'status.in' => 'ສະຖານະ ຕ້ອງເປັນ active ຫຼື inactive.',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function getFailures()
    {
        return $this->failures;
    }
}
