@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-6">
{{--                <h1 class="text-2xl font-bold text-gray-800">Service on {{ $service->service_date->format('F j, Y') }}</h1>--}}
                <h1 class="text-2xl font-bold text-gray-800">Service on {{ $service->service_date }}</h1>

                <div class="flex space-x-2">
                    <a href="{{ route('services.edit', $service->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition">
                        Edit
                    </a>
                    <a href="{{ route('services.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                        Back to Services
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Service Details</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Description</p>
                                <p class="text-gray-800">{{ $service->description ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Financial Summary</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Opening Balance</p>
                                <p class="text-gray-800">${{ number_format($service->opening_balance, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Closing Balance</p>
                                <p class="text-gray-800">${{ number_format($service->closing_balance, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Service Transactions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-medium text-blue-800 mb-2">Incomes</h3>
                            <p class="text-sm">Total Offerings: ${{ number_format($service->incomes()->where('type', 'offering')->sum('amount'), 2) }}</p>
                            <p class="text-sm">Total Partnerships: ${{ number_format($service->incomes()->where('type', 'partnership')->sum('amount'), 2) }}</p>
                            <p class="text-sm">Total Tuckshop: ${{ number_format($service->tuckshopSales()->sum('amount'), 2) }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <h3 class="font-medium text-red-800 mb-2">Expenses</h3>
                            <p class="text-sm">Total Expenses: ${{ number_format($service->expenses()->sum('amount'), 2) }}</p>
                            <p class="text-sm">By Category:</p>
                            <ul class="text-sm list-disc list-inside">
                                @foreach($service->expenses()->select('category')->groupBy('category')->selectRaw('sum(amount) as total')->get() as $expense)
                                    <li>{{ ucfirst($expense->category) }}: ${{ number_format($expense->total, 2) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
