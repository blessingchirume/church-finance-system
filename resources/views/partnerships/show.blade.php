@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Partnership Details</h1>
        <a href="{{ route('partnerships.index') }}" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">{{ $history['partner']['name'] }}</h2>
            <p class="text-gray-600">{{ $history['partner']['email'] }}</p>
        </div>
        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Monthly Commitment</p>
                <p class="font-semibold">${{ number_format($history['partner']['commitment'], 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Start Date</p>
                <p class="font-semibold">{{ $history['partner']['start_date'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-semibold">
                    <span class="relative inline-block px-3 py-1 font-semibold {{ $history['partner']['is_active'] ? 'text-green-900 bg-green-200' : 'text-gray-900 bg-gray-200' }} rounded-full">
                        {{ $history['partner']['is_active'] ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Arrears</p>
                <p class="font-semibold {{ $history['total_arrears'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                    ${{ number_format($history['total_arrears'], 2) }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Payment History</h2>
        </div>
        @if(count($history['payments']) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Method
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Reference
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Month
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history['payments'] as $payment)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $payment['date'] }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            ${{ number_format($payment['amount'], 2) }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ ucfirst($payment['method']) }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $payment['reference'] ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $payment['month'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-4">
            <p class="text-gray-500">No payments recorded yet.</p>
        </div>
        @endif
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Arrears</h2>
        </div>
        @if(count($history['arrears']) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Month
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Expected
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Paid
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Balance
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history['arrears'] as $arrear)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $arrear['month'] }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            ${{ number_format($arrear['expected'], 2) }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            ${{ number_format($arrear['paid'], 2) }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm {{ $arrear['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ${{ number_format($arrear['balance'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-4">
            <p class="text-gray-500">No arrears recorded.</p>
        </div>
        @endif
    </div>

    <div class="mt-6">
        <a href="{{ route('partnerships.record-payment', $history['partner']['id']) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition duration-300 inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
            Record Payment
        </a>
        <a href="{{ route('partnerships.index') }}" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition duration-300 inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to List
        </a>
    </div>
</div>
@endsection