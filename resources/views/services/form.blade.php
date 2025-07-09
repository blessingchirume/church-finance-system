@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($service) ? 'Edit' : 'Add' }} Service</h1>

            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ isset($service) ? route('services.update', $service->id) : route('services.store') }}">
                    @csrf
                    @isset($service) @method('PUT') @endisset

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="service_date" class="block text-sm font-medium text-gray-700">Service Date *</label>
                            <input type="date" id="service_date" name="service_date" value="{{ isset($service) ? $service->service_date : old('service_date') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $service->description ?? old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="opening_balance" class="block text-sm font-medium text-gray-700">Opening Balance *</label>
                                <input type="number" step="0.01" id="opening_balance" name="opening_balance" min="0" value="{{ $service->opening_balance ?? old('opening_balance') ?? '0.00' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="closing_balance" class="block text-sm font-medium text-gray-700">Closing Balance *</label>
                                <input type="number" step="0.01" id="closing_balance" name="closing_balance" min="0" value="{{ $service->closing_balance ?? old('closing_balance') ?? '0.00' }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ isset($service) ? route('services.show', $service->id) : route('services.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">
                            {{ isset($service) ? 'Update' : 'Save' }} Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
