<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Carbon; // Import Carbon

class ReportExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $reportData;
    protected $totalIncome;
    protected $totalExpense;
    protected $startDate;
    protected $endDate;

    public function __construct(Collection $reportData, float $totalIncome, float $totalExpense, string $startDate, string $endDate)
    {
        $this->reportData = $reportData;
        $this->totalIncome = $totalIncome;
        $this->totalExpense = $totalExpense;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = new Collection();

        // Title and dates
        $data->push(['ລາຍງານປະຈຳວັນທີ່ ' . $this->formatDate($this->startDate) . ' ຫາ ' . $this->formatDate($this->endDate)]);
        $data->push([]); // Blank row
        
        // Summary
        $data->push(['ລາຍຮັບທັງໝົດ:', $this->totalIncome]);
        $data->push(['ລາຍຈ່າຍທັງໝົດ:', $this->totalExpense]);
        $data->push([]); // Blank row

        // Report Table Headers
        $data->push($this->headings());

        // Report Data
        $counter = 1;
        foreach ($this->reportData as $item) {
            $data->push([
                $counter++,
                $this->formatDate($item['date']),
                $item['description'],
                $item['type'] === 'income' ? 'ລາຍຮັບ' : 'ລາຍຈ່າຍ',
                $item['amount'],
            ]);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'ວັນທີ່',
            'ລາຍລະອຽດ',
            'ປະເພດ',
            'ຈຳນວນ',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Get the highest row and column that contains data
                $highestRow = $sheet->getHighestRow(); // e.g. 10
                $highestColumn = $sheet->getHighestColumn(); // e.g. 'E'
                $range = 'A1:' . $highestColumn . $highestRow;
                $sheet->getStyle($range)->getFont()->setName('Phetsarath OT')->setSize(12);

                // Style for Title
                $sheet->mergeCells('A1:E1'); // Merge across all columns for the title
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // Style for Summary
                // Summary starts from row 3 (after title row 1, blank row 2)
                $sheet->getStyle('A3:B4')->getFont()->setBold(true);
                $sheet->getStyle('B3')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('B4')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

                // Style for Table Headers
                $headerRow = 6; // After title (1), blank (1), summary (2), blank (1) = 5. So header is at row 6.
                $sheet->getStyle('A' . $headerRow . ':E' . $headerRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $headerRow . ':E' . $headerRow)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFA0A0A0'); // Grey background
            
                // Set currency format for 'ຈຳນວນ' column (Column E)
                $dataRowsStart = $headerRow + 1;
                $dataRowsEnd = $dataRowsStart + count($this->reportData) - 1;
                if ($dataRowsEnd >= $dataRowsStart) { // Only apply if there is data
                    $sheet->getStyle('E' . $dataRowsStart . ':E' . $dataRowsEnd)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1); // Example: 1,234.00
                }
            },
        ];
    }

    private function formatDate(string $dateString): string
    {
        return Carbon::parse($dateString)->format('d-m-Y');
    }
}
