@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Member Details</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('members.edit', $member->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition duration-300">
                        Edit
                    </a>
                    <a href="{{ route('members.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-300">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Basic Information</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="text-gray-800">{{ $member->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="inline-block px-2 py-1 text-xs font-semibold {{ $member->status === 'active' ? 'text-green-800 bg-green-200' : 'text-red-800 bg-red-200' }} rounded-full">
                                {{ ucfirst($member->status) }}
                            </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Contact Information</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-gray-800">{{ $member->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="text-gray-800">{{ $member->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Timestamps</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Created At</p>
                            <p class="text-gray-800">{{ $member->created_at }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Updated At</p>
                            <p class="text-gray-800">{{ $member->updated_at }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
