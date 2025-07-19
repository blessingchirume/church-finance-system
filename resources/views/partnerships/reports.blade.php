@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Partnership Reports</h1>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('partnerships.reports') }}" method="GET" class="flex items-end space-x-4">
            <div class="flex-1">
                <label for="month" class="block text-gray-700 text-sm font-bold mb-2">Select Month</label>
                <input type="month" name="month" id="month" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="{{ $month }}">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-300">
                Filter
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Expected Income</h3>
            <p class="text-2xl font-bold text-blue-600">${{ number_format($expectedIncome, 2) }}</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Actual Income</h3>
            <p class="text-2xl font-bold text-green-600">${{ number_format($actualIncome, 2) }}</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Arrears</h3>
            <p class="text-2xl font-bold {{ $report['total_arrears'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                ${{ number_format($report['total_arrears'], 2) }}
            </p>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Partners in Arrears</h2>
        </div>
        @if(count($report['partners_in_arrears']) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Member
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
                    @foreach($report['partners_in_arrears'] as $partner)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <div class="w-full h-full rounded-full bg-blue-500 text-white flex items-center justify-center">
                                        {{ substr($partner['name'], 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ $partner['name'] }}
                                    </p>
                                    <p class="text-gray-500 text-xs">
                                        {{ $partner['email'] }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            ${{ number_format($partner['expected'], 2) }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            ${{ number_format($partner['paid'], 2) }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-red-600">
                            ${{ number_format($partner['balance'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-4">
            <p class="text-gray-500">No partners in arrears for this month.</p>
        </div>
        @endif
    </div>

    <div class="mt-6">
        <a href="{{ route('partnerships.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition duration-300 inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Partnerships
        </a>
    </div>
</div>
@endsection