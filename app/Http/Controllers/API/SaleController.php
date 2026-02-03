<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException; // To catch validation errors

class SaleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // 1. Validation
            $validatedData = $request->validate([
                'customer_id' => 'nullable|exists:customers,id',
                'discount' => 'numeric|min:0',
                'cart_items' => 'required|array|min:1',
                'cart_items.*.id' => 'required|exists:products,id',
                'cart_items.*.qty' => 'required|integer|min:1',
                'cart_items.*.price' => 'required|numeric|min:0', // price at time of sale

                'payments' => 'required|array|min:1',
                'payments.*.amount' => 'required|numeric|min:0',
                'payments.*.method' => 'required|string|in:cash,transfer',

                'total_amount' => 'required|numeric|min:0',
                'total_received' => 'required|numeric|min:0',
                'change_due' => 'required|numeric', // Can be negative if insufficient payment, handled in frontend
            ]);

            // 2. Database Transaction
            $sale = DB::transaction(function () use ($validatedData) {
                // Generate a unique invoice number (simple example)
                $invoiceNumber = 'INV-' . Str::upper(Str::random(8));
                while (Sale::where('invoice_number', $invoiceNumber)->exists()) {
                    $invoiceNumber = 'INV-' . Str::upper(Str::random(8));
                }

                // Create Sale
                $sale = Sale::create([
                    'invoice_number' => $invoiceNumber,
                    'customer_id' => $validatedData['customer_id'] ?? null,
                    'user_id' => auth()->id(), // Assuming authenticated user is making the sale
                    'total_amount' => $validatedData['total_amount'],
                    'discount' => $validatedData['discount'],
                    'total_received' => $validatedData['total_received'],
                    'change_due' => $validatedData['change_due'],
                    'status' => 'completed', // Or 'pending' if change_due is negative
                    'sale_date' => now(),
                ]);

                // Create Sale Items and update product stock
                foreach ($validatedData['cart_items'] as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['qty'],
                        'price' => $item['price'],
                        'total' => $item['qty'] * $item['price'], // Calculate total
                    ]);

                    // Update product stock
                    $product = Product::find($item['id']);
                    if ($product) {
                        // Ensure stock doesn't go negative from concurrent sales
                        if ($product->stock_quantity < $item['qty']) {
                            // This situation should ideally be rare due to frontend checks,
                            // but this is a failsafe.
                            throw new \Exception('Insufficient stock for product ID: ' . $item['id']);
                        }
                        $product->stock_quantity -= $item['qty'];
                        $product->save();
                    }
                }

                // Create Payments
                foreach ($validatedData['payments'] as $payment) {
                    Payment::create([
                        'sale_id' => $sale->id,
                        'amount' => $payment['amount'],
                        'method' => $payment['method'],
                    ]);
                }

                return $sale;
            });

            // 3. Return Response
            return response()->json([
                'message' => 'Sale completed successfully!',
                'sale' => $sale->load(['saleItems.product', 'payments', 'customer']), // Load relationships for response
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error("SaleController store error: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'An unexpected error occurred during the sale transaction.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Unused methods (for API resource, but we only need store for now)
    public function index() {}
    public function show(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}