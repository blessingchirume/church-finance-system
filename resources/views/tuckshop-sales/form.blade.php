@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($tuckshopSale) ? 'Edit' : 'Add' }} Tuckshop Sale</h1>

            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ isset($tuckshopSale) ? route('tuckshop-sales.update', $tuckshopSale->id) : route('tuckshop-sales.store') }}">
                    @csrf
                    @isset($tuckshopSale) @method('PUT') @endisset

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="external_reference" class="block text-sm font-medium text-gray-700">External Reference *</label>
                            <input type="text" id="external_reference" name="external_reference" value="{{ $tuckshopSale->external_reference ?? old('external_reference') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-700">Service</label>
                            <select id="service_id" name="service_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ (isset($tuckshopSale) && $tuckshopSale->service_id == $service->id) || old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->service_date->format('M d, Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                            <input type="number" step="0.01" id="amount" name="amount" min="0" value="{{ $tuckshopSale->amount ?? old('amount') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('tuckshop-sales.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">
                            {{ isset($tuckshopSale) ? 'Update' : 'Save' }} Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
