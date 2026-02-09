@extends('layout')

<style>
    /* Dark mode variables */
    .dark {
        --tl-cream: #4a4a4a;
        --tl-parchment: #5a5a5a;
        --tl-stone: #6a6a6a;
        --tl-warm-gray: #c0c0c0;
        --tl-charcoal: #f0f0f0;
        --tl-terracotta: #D4856A;
        --tl-terracotta-light: #B85C38;
        --tl-sage: #6B9B6F;
        --tl-sage-light: #4A7C59;
        --tl-amber: #E5C457;
        --tl-amber-light: #C9A227;
        --tl-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.2);
        --tl-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.3), 0 2px 4px rgba(0, 0, 0, 0.2);
        --tl-shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.4), 0 4px 12px rgba(0, 0, 0, 0.2);
        --tl-shadow-hover: 0 20px 50px rgba(0, 0, 0, 0.5), 0 8px 20px rgba(0, 0, 0, 0.3);
    }
</style>

@section('content')
<div class="flex-1 overflow-y-auto">
    <div class="p-6 md:p-10 max-w-7xl mx-auto animate-fade">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Travel Overview</h1>
            <p class="text-gray-600">Track your adventures and plan your next journey</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white rounded-2xl p-8 card-shadow card-shadow-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-900 text-sm font-bold mb-1 uppercase tracking-tight">Total Trips</p>
                        <p class="text-4xl font-black text-gray-900">{{ $trips->count() }}</p>
                    </div>
                    <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center text-white shadow-lg transform hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-globe text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 card-shadow card-shadow-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-900 text-sm font-bold mb-1 uppercase tracking-tight">Avg Rating</p>
                        <p class="text-4xl font-black text-gray-900">{{ $trips->count() > 0 ? number_format($trips->avg('rating'), 1) : '0' }}</p>
                        <p class="text-xs text-yellow-500 mt-1">{{ $trips->count() > 0 ? str_repeat('★', round($trips->avg('rating'))) : 'No trips' }}</p>
                    </div>
                    <div class="w-16 h-16 gradient-success rounded-2xl flex items-center justify-center text-white shadow-lg transform hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-star text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 card-shadow card-shadow-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-900 text-sm font-bold mb-1 uppercase tracking-tight">Unique Places</p>
                        <p class="text-4xl font-black text-gray-900">{{ $trips->pluck('destination')->unique()->count() }}</p>
                    </div>
                    <div class="w-16 h-16 gradient-warning rounded-2xl flex items-center justify-center text-white shadow-lg transform hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-map-pin text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-8 card-shadow card-shadow-hover border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-900 text-sm font-bold mb-1 uppercase tracking-tight">To Visit</p>
                        <p class="text-4xl font-black text-gray-900">{{ $buckets->count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $buckets->count() > 0 ? 'in bucket list' : 'Check bucket list' }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center text-white shadow-lg transform hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-list-check text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

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
            <div class="mb-6 flex justify-center">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-50 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-globe text-5xl text-blue-600"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No trips yet</h3>
            <p class="text-gray-600 mb-6">Start logging your travels to see them here</p>
            <a href="{{ url('/map') }}" class="inline-block px-8 py-3 gradient-primary text-white rounded-lg font-semibold hover:shadow-lg transition-all transform hover:scale-105 duration-200">
                Add Your First Trip
            </a>
        </div>
        @endif
    </div>
</div>
@endsection