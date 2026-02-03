<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\StockImport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Barryvdh\DomPDF\Facade\Pdf; // Add this line

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        [$reportData, $totalIncome, $totalExpense] = $this->getReportData($validated['start_date'], $validated['end_date']);

        return response()->json([
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'report_data' => $reportData,
        ]);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        [$reportData, $totalIncome, $totalExpense] = $this->getReportData($validated['start_date'], $validated['end_date']);

        $filename = 'report_' . $validated['start_date'] . '_to_' . $validated['end_date'] . '.xlsx';

        return Excel::download(new ReportExport($reportData, $totalIncome, $totalExpense, $validated['start_date'], $validated['end_date']), $filename);
    }

    /**
     * Export the report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        [$reportData, $totalIncome, $totalExpense] = $this->getReportData($validated['start_date'], $validated['end_date']);

        $data = [
            'reportData' => $reportData,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'startDate' => Carbon::parse($validated['start_date']),
            'endDate' => Carbon::parse($validated['end_date']),
        ];

        $pdf = Pdf::loadView('reports.pdf_template', $data);

        $filename = 'report_' . $validated['start_date'] . '_to_' . $validated['end_date'] . '.pdf';

        return $pdf->download($filename);
    }


    /**
     * Helper method to get report data.
     * Moved the logic from original index and export methods here.
     */
    private function getReportData(string $startDateString, string $endDateString): array
    {
        $startDate = Carbon::parse($startDateString)->startOfDay();
        $endDate = Carbon::parse($endDateString)->endOfDay();

        // 2. Fetch Income (Sales)
        $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->select('sale_date as date', 'total_amount as amount', 'invoice_number')
            ->get()
            ->map(function ($sale) {
                return [
                    'date' => $sale->date,
                    'description' => 'ລາຍຮັບຈາກການຂາຍ (Invoice: ' . $sale->invoice_number . ')',
                    'type' => 'income',
                    'amount' => (float) $sale->amount,
                ];
            });

        // 3. Fetch Expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->select('expense_date as date', 'amount', 'description')
            ->get()
            ->map(function ($expense) {
                return [
                    'date' => $expense->date,
                    'description' => $expense->description,
                    'type' => 'expense',
                    'amount' => (float) $expense->amount,
                ];
            });

        // 4. Fetch Stock Imports as Expenses
        $stockImports = StockImport::whereBetween('import_date', [$startDate, $endDate])
            ->select('import_date as date', 'total_cost as amount', 'invoice_number')
            ->get()
            ->map(function ($import) {
                return [
                    'date' => $import->date,
                    'description' => 'ລາຍຈ່າຍນຳເຂົ້າສິນຄ້າ (Invoice: ' . $import->invoice_number . ')',
                    'type' => 'expense',
                    'amount' => (float) $import->amount,
                ];
            });

        // 5. Calculate Totals
        $totalIncome = $sales->sum('amount');
        $totalExpense = $expenses->sum('amount') + $stockImports->sum('amount');

        // 6. Merge, Sort, and Format Data
        $reportData = collect([])
            ->merge($sales)
            ->merge($expenses)
            ->merge($stockImports)
            ->sortBy('date') // Sort by date ascending
            ->values(); // Reset array keys

        return [$reportData, $totalIncome, $totalExpense];
    }
}
