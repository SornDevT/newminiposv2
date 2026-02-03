<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StockImport;
use App\Models\StockImportItem;
use App\Models\Product;
use App\Http\Requests\StoreStockImportRequest;
use App\Http\Requests\UpdateStockImportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockImportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StockImportItem::with(['product', 'stockImport.user'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });

        $stockImportItems = $query->latest('created_at')->paginate(10);

        // Map the items to a flatter structure for the frontend
        $mappedItems = $stockImportItems->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'cost_price_per_unit' => $item->cost_price, // map to frontend name
                'new_selling_price' => $item->product->price, // current product price
                'description' => $item->stockImport->invoice_number, // using invoice_number as description
                'import_date' => $item->stockImport->import_date,
                'product' => [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                ],
                'user_id' => $item->stockImport->user_id,
                'user_name' => $item->stockImport->user->name ?? 'N/A',
            ];
        });

        return response()->json([
            'data' => $mappedItems,
            'current_page' => $stockImportItems->currentPage(),
            'last_page' => $stockImportItems->lastPage(),
            'per_page' => $stockImportItems->perPage(),
            'total' => $stockImportItems->total(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockImportRequest $request)
    {
        $validated = $request->validated();
        $product = Product::findOrFail($validated['product_id']);

        DB::beginTransaction();
        try {
            // Create a parent StockImport (header)
            $stockImport = StockImport::create([
                'user_id' => $request->user()->id,
                'invoice_number' => 'IMP-' . Str::upper(Str::random(8)),
                'total_cost' => $validated['quantity'] * $validated['cost_price_per_unit'],
                'import_date' => now(),
                'status' => 'completed',
                'supplier_id' => $validated['supplier_id'], // Can be added if frontend provides it
            ]);

            // Create StockImportItem (line item)
            $stockImportItem = $stockImport->stockImportItems()->create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'cost_price' => $validated['cost_price_per_unit'],
                'total' => $validated['quantity'] * $validated['cost_price_per_unit'],
            ]);

            // Update product stock and price
            $product->increment('stock_quantity', $validated['quantity']);
            $product->cost_price = $validated['cost_price_per_unit']; // Update product's base cost price
            if (isset($validated['new_selling_price'])) {
                $product->price = $validated['new_selling_price'];
            }
            $product->save();

            DB::commit();

            return response()->json($stockImportItem->load(['product', 'stockImport.user']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to store stock import.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockImportItem $stockImportItem)
    {
        return response()->json($stockImportItem->load(['product', 'stockImport.user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockImportRequest $request, StockImportItem $stockImportItem)
    {
        $validated = $request->validated();
        $product = Product::findOrFail($validated['product_id']);

        DB::beginTransaction();
        try {
            // Calculate old stock impact
            $oldQuantity = $stockImportItem->quantity;
            $oldCostPrice = $stockImportItem->cost_price;

            // Update StockImportItem
            $stockImportItem->update([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'cost_price' => $validated['cost_price_per_unit'],
                'total' => $validated['quantity'] * $validated['cost_price_per_unit'],
            ]);

            // Update product stock
            $quantityDifference = $validated['quantity'] - $oldQuantity;
            $product->increment('stock_quantity', $quantityDifference);
            $product->cost_price = $validated['cost_price_per_unit']; // Update product's base cost price
            if (isset($validated['new_selling_price'])) {
                $product->price = $validated['new_selling_price'];
            }
            $product->save();

            // Update parent StockImport total cost
            $parentImport = $stockImportItem->stockImport;
            $parentImport->total_cost = $parentImport->stockImportItems->sum('total');
            $parentImport->save();

            DB::commit();

            return response()->json($stockImportItem->load(['product', 'stockImport.user']), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update stock import.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockImportItem $stockImportItem)
    {
        DB::beginTransaction();
        try {
            $product = $stockImportItem->product;
            $parentImport = $stockImportItem->stockImport;

            // Decrement product stock
            $product->decrement('stock_quantity', $stockImportItem->quantity);
            $product->save();

            // Delete StockImportItem
            $stockImportItem->delete();

            // Update parent StockImport total cost
            $parentImport->total_cost = $parentImport->stockImportItems->sum('total');
            $parentImport->save();

            // If no more items in parent StockImport, delete the parent
            if ($parentImport->stockImportItems->count() === 0) {
                $parentImport->delete();
            }

            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete stock import.', 'error' => $e->getMessage()], 500);
        }
    }
}
