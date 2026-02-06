@extends('layout')

@section('content')
<div class="flex-1 overflow-y-auto">
    <div class="p-6 md:p-10 max-w-7xl mx-auto animate-fade">
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Travel Overview</h1>
            <p class="text-gray-600">Track your adventures and plan your next journey</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Total Trips -->
            <div class="bg-white rounded-2xl p-8 card-shadow card-shadow-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Total Trips</p>
                        <p class="text-4xl font-bold text-gray-900">{{ $trips->count() }}</p>
                    </div>
                    <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center text-white text-2xl">🗺️</div>
                </div>
            </div>

            <!-- Average Rating -->
            <div class="bg-white rounded-2xl p-8 card-shadow card-shadow-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Avg Rating</p>
                        <p class="text-4xl font-bold text-gray-900">{{ $trips->count() > 0 ? number_format($trips->avg('rating'), 1) : '0' }}</p>
                        <p class="text-xs text-yellow-500 mt-1">{{ $trips->count() > 0 ? str_repeat('★', round($trips->avg('rating'))) : 'No trips' }}</p>
                    </div>
                    <div class="w-16 h-16 gradient-success rounded-2xl flex items-center justify-center text-white text-2xl">⭐</div>
                </div>
            </div>

            <!-- Unique Places -->
            <div class="bg-white rounded-2xl p-8 card-shadow card-shadow-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">Unique Places</p>
                        <p class="text-4xl font-bold text-gray-900">{{ $trips->pluck('destination')->unique()->count() }}</p>
                    </div>
                    <div class="w-16 h-16 gradient-warning rounded-2xl flex items-center justify-center text-white text-2xl">📍</div>
                </div>
            </div>

            <!-- Bucket List -->
            <div class="bg-white rounded-2xl p-8 card-shadow card-shadow-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium mb-1">To Visit</p>
                        <p class="text-4xl font-bold text-gray-900">--</p>
                        <p class="text-xs text-gray-500 mt-1">Check bucket list</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center text-white text-2xl">✨</div>
                </div>
            </div>
        </div>

        <!-- Recent Trips Section -->
        @if($trips->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden card-shadow">
            <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-gray-100 flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-lg text-gray-900">Recent Trips</h2>
                    <p class="text-sm text-gray-600">Your latest travel memories</p>
                </div>
                <a href="{{ url('/map') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">View all →</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Destination</th>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Date</th>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Rating</th>
                            <th class="px-8 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($trips->take(8) as $trip)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-8 py-5 font-semibold text-gray-900">{{ $trip->destination }}</td>
                            <td class="px-8 py-5 text-gray-600 text-sm">{{ \Carbon\Carbon::parse($trip->date)->format('M d, Y') }}</td>
                            <td class="px-8 py-5 text-yellow-500 font-semibold">{{ str_repeat('★', $trip->rating) }}</td>
                            <td class="px-8 py-5">
                                <a href="{{ url('/map') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-gray-100 py-16 text-center card-shadow">
            <div class="mb-4 flex justify-center">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full flex items-center justify-center text-4xl">🗺️</div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No trips yet</h3>
            <p class="text-gray-600 mb-6">Start logging your travels to see them here</p>
            <a href="{{ url('/map') }}" class="inline-block px-6 py-3 gradient-primary text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                Add Your First Trip
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
