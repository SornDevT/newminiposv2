<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ລາຍງານປະຈຳວັນ</title>
    <style>
        @font-face {
            font-family: 'Phetsarath OT';
            src: url('{{ storage_path("fonts/PhetsarathOT.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: "Phetsarath OT"; /* Specify Lao font */
            font-size: 10pt;
            margin: 20px;
        }
        h1{
            font-size: 18pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold;
        font-family: "Phetsarath OT";
         }
        .text-success { color: green; }
        .text-error { color: red; }
        .summary { margin-bottom: 20px; }
        .summary p { margin: 0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18pt; }
        .header p { margin: 0; font-size: 12pt; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ລາຍງານປະຈຳວັນ</h1>
        <p>ວັນທີ່ {{ $startDate->format('d-m-Y') }} ຫາ {{ $endDate->format('d-m-Y') }}</p>
    </div>

    <div class="summary">
        <p class="font-bold">ລາຍຮັບທັງໝົດ: <span class="text-success">{{ number_format($totalIncome, 0, ',', '.') }} LAK</span></p>
        <p class="font-bold">ລາຍຈ່າຍທັງໝົດ: <span class="text-error">{{ number_format($totalExpense, 0, ',', '.') }} LAK</span></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>ວັນທີ່</th>
                <th>ລາຍລະອຽດ</th>
                <th>ປະເພດ</th>
                <th>ຈຳນວນ</th>
            </tr>
        </thead>
        <tbody>
            @if($reportData->isEmpty())
                <tr>
                    <td colspan="5" class="text-center">ບໍ່ມີຂໍ້ມູນ</td>
                </tr>
            @else
                @foreach($reportData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item['date'])->format('d-m-Y') }}</td>
                        <td>{{ $item['description'] }}</td>
                        <td>{{ $item['type'] === 'income' ? 'ລາຍຮັບ' : 'ລາຍຈ່າຍ' }}</td>
                        <td class="text-right {{ $item['type'] === 'income' ? 'text-success' : 'text-error' }}">
                            {{ number_format($item['amount'], 0, ',', '.') }} LAK
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>
