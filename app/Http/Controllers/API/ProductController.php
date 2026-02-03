<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest; // Import the Form Request
use App\Http\Requests\UpdateProductRequest; // Import the Update Form Request
use Illuminate\Support\Facades\Storage; // Import Storage Facade
use Illuminate\Support\Str; // Import Str Facade
use Maatwebsite\Excel\Facades\Excel; // Add this line
use App\Imports\ProductImport; // Add this line


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with(['category', 'unit'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhereHas('category', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('unit', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
            })
            ->latest();
        if ($request->get('page') === '-1') {
            return $products->get();
        }
        $products = $products->paginate(10);

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public'); // Store relative path in public disk
            $validated['image'] = $imagePath; // Store relative path in DB
        }

        $product = Product::create($validated);

        return response()->json($product->load(['category', 'unit']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json($product->load(['category', 'unit']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public'); // Store relative path in public disk
            $validated['image'] = $imagePath; // Store relative path in DB

        } elseif ($request->input('image_removed')) { // Handle explicit image removal from frontend
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
                $validated['image'] = null;
            }
        }


        $product->update($validated);

        return response()->json($product->load(['category', 'unit']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * Import products from an Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $import = new ProductImport();
        Excel::import($import, $request->file('file'));

        if ($import->getFailures()->isNotEmpty()) {
            $errors = $import->getFailures()->map(function ($failure) {
                return [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            });
            return response()->json([
                'message' => 'ການນຳເຂົ້າສຳເລັດດ້ວຍບາງຂໍ້ຜິດພາດ.',
                'failures' => $errors
            ], 422);
        }

        return response()->json(['message' => 'ການນຳເຂົ້າສິນຄ້າສຳເລັດ!']);
    }
}
