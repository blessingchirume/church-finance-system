@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($contribution) ? 'Edit' : 'Add' }} Funeral Contribution</h1>

            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ isset($contribution) ? route('funeral-contributions.update', $contribution->id) : route('funeral-contributions.store') }}">
                    @csrf
                    @isset($contribution) @method('PUT') @endisset

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label for="member_id" class="block text-sm font-medium text-gray-700">Member *</label>
                            <select id="member_id" name="member_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ (isset($contribution) && $contribution->member_id == $member->id) || old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700">Year *</label>
                            <input type="number" id="year" name="year" min="2000" max="{{ date('Y') + 1 }}" value="{{ $contribution->year ?? old('year') ?? date('Y') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                            <input type="number" step="0.01" id="amount" name="amount" min="0" value="{{ $contribution->amount ?? old('amount') ?? '10.00' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="paid_at" class="block text-sm font-medium text-gray-700">Payment Date</label>
                            <input type="date" id="paid_at" name="paid_at" value="{{ isset($contribution) ? ($contribution->paid_at?->format('Y-m-d') ?? '') : old('paid_at') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('funeral-contributions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">
                            {{ isset($contribution) ? 'Update' : 'Save' }} Contribution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
