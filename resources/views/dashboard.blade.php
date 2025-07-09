@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Church Management Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Members Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Members</h3>
                        <p class="mt-1 text-3xl font-semibold text-blue-600">{{ App\Models\Member::count() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('members.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        View all members
                    </a>
                </div>
            </div>

            <!-- Recent Services Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Recent Services</h3>
                        <p class="mt-1 text-3xl font-semibold text-green-600">{{ App\Models\Service::whereDate('service_date', '>=', now()->subMonth())->count() }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('services.index') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                        View all services
                    </a>
                </div>
            </div>

            <!-- Active Projects Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Active Projects</h3>
                        <p class="mt-1 text-3xl font-semibold text-purple-600">{{ App\Models\Project::where('status', 'active')->count() }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('projects.index') }}" class="text-sm font-medium text-purple-600 hover:text-purple-500">
                        View all projects
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Incomes -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Incomes</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach(App\Models\Income::latest()->take(5)->get() as $income)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 capitalize">{{ $income->type }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($income->member)
                                                {{ $income->member->name }}
                                            @elseif($income->service)
                                                Service: {{ $income->service->service_date }}
                                            @elseif($income->project)
                                                Project: {{ $income->project->name }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm font-semibold text-green-600">
                                    ${{ number_format($income->amount, 2) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-gray-50">
                    <a href="{{ route('incomes.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        View all incomes
                    </a>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Expenses</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach(App\Models\Expense::with('service')->latest()->take(5)->get() as $expense)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 capitalize">{{ $expense->category }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $expense->service->service_date }} - {{ Str::limit($expense->description, 30) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm font-semibold text-red-600">
                                    ${{ number_format($expense->amount, 2) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-gray-50">
                    <a href="{{ route('expenses.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        View all expenses
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
