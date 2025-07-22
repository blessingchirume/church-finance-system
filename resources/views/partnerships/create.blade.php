@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Register New Partnership</h1>
            <a href="{{ route('partnerships.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('partnerships.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="member_id" class="block text-gray-700 text-sm font-bold mb-2">Member</label>
                    <select  name="member_id" id="user_id" class="select2 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 form-control" required>
                        <option value="">Select Member</option>
                        @foreach($members as $member)
                        <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Monthly Commitment Amount</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                        <input type="number" step="0.01" min="1" name="amount" id="amount"
                               class="w-full pl-8 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="0.00" required>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-300">
                        Register Partnership
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
