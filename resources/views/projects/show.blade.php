@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">{{ $project->name }}</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('projects.edit', $project->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition">
                        Edit
                    </a>
                    <a href="{{ route('projects.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                        Back to Projects
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Project Details</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <p class="text-gray-800 capitalize">{{ $project->status }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Description</p>
                                <p class="text-gray-800">{{ $project->description ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Timeline</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Start Date</p>
                                <p class="text-gray-800">{{ $project->start_date?->format('M d, Y') ?? 'Not started' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">End Date</p>
                                <p class="text-gray-800">{{ $project->end_date?->format('M d, Y') ?? 'Ongoing' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Project Finances</h2>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500">Total Pledges: ${{ number_format($project->incomes()->where('type', 'project_pledge')->sum('amount'), 2) }}</p>
                        <p class="text-sm text-gray-500">Total Contributions: ${{ number_format($project->incomes()->sum('amount'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
