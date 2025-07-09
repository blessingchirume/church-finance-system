@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($income) ? 'Edit' : 'Add' }} Income Record</h1>

            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ isset($income) ? route('incomes.update', $income->id) : route('incomes.store') }}">
                    @csrf
                    @isset($income) @method('PUT') @endisset

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Income Type *</label>
                            <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ (isset($income) && $income->type == $type) || old('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount *</label>
                            <input type="number" step="0.01" id="amount" name="amount" min="0" value="{{ $income->amount ?? old('amount') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="member_id" class="block text-sm font-medium text-gray-700">Member</label>
                            <select id="member_id" name="member_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ (isset($income) && $income->member_id == $member->id) || old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="service_id" class="block text-sm font-medium text-gray-700">Service</label>
                            <select id="service_id" name="service_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ (isset($income) && $income->service_id == $service->id) || old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->service_date }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="project_id" class="block text-sm font-medium text-gray-700">Project</label>
                            <select id="project_id" name="project_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ (isset($income) && $income->project_id == $project->id) || old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="source" class="block text-sm font-medium text-gray-700">Source *</label>
                            <select id="source" name="source" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach($sources as $source)
                                    <option value="{{ $source }}" {{ (isset($income) && $income->source == $source) || old('source') == $source ? 'selected' : '' }}>
                                        {{ ucfirst($source) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $income->description ?? old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('incomes.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">
                            {{ isset($income) ? 'Update' : 'Save' }} Income
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
