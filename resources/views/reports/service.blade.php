<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Service Report - {{ $service->service_date }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            border: 1px solid #888;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .totals {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .balance {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Church Financial Report</h2>
<p><strong>Service Date:</strong> {{ \Carbon\Carbon::parse($service->service_date)->format('F d, Y') }}</p>

<div class="section-title">Income Breakdown</div>
<table>
    <thead>
    <tr>
        <th>Type</th>
        <th>Member</th>
        <th>Amount</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    @php $totalIncome = 0; @endphp
    @foreach($service->incomes as $income)
        <tr>
            <td>{{ ucfirst($income->type) }}</td>
            <td>{{ $income->member->name ?? 'N/A' }}</td>
            <td>${{ number_format($income->amount, 2) }}</td>
            <td>{{ $income->description }}</td>
        </tr>
        @php $totalIncome += $income->amount; @endphp
    @endforeach
    <tr class="totals">
        <td colspan="2">Total Income</td>
        <td colspan="2">${{ number_format($totalIncome, 2) }}</td>
    </tr>
    </tbody>
</table>

<div class="section-title">Expense Breakdown</div>
<table>
    <thead>
    <tr>
        <th>Category</th>
        <th>Description</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @php $totalExpenses = 0; @endphp
    @foreach($service->expenses as $expense)
        <tr>
            <td>{{ $expense->category }}</td>
            <td>{{ $expense->description }}</td>
            <td>${{ number_format($expense->amount, 2) }}</td>
        </tr>
        @php $totalExpenses += $expense->amount; @endphp
    @endforeach
    <tr class="totals">
        <td colspan="2">Total Expenses</td>
        <td>${{ number_format($totalExpenses, 2) }}</td>
    </tr>
    </tbody>
</table>

<div class="balance">
    <p><strong>Opening Balance:</strong> ${{ number_format($service->opening_balance, 2) }}</p>
    <p><strong>Closing Balance:</strong>
        ${{ number_format($service->opening_balance + $totalIncome - $totalExpenses, 2) }}</p>
</div>

</body>
</html>
