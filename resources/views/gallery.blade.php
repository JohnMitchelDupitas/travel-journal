@extends('layout')

@section('content')
<div class="flex-1 overflow-y-auto">
    <div class="p-6 md:p-10 max-w-7xl mx-auto animate-fade">
        <!-- Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Memory Gallery</h1>
            <p class="text-gray-600">{{ $trips->count() }} beautiful moments captured in time</p>
        </div>
        
        @if($trips->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($trips as $trip)
                <div class="group relative overflow-hidden rounded-xl aspect-square cursor-pointer shadow-md card-shadow-hover">
                    <!-- Image -->
                    <img src="{{ asset($trip->image) }}" alt="{{ $trip->destination }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-125">
                    
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-end justify-end p-4">
                        <div class="text-white text-right w-full">
                            <p class="font-bold text-lg group-hover:text-xl transition-all duration-300">{{ $trip->destination }}</p>
                            <p class="text-xs text-gray-200 mt-1">{{ \Carbon\Carbon::parse($trip->date)->format('M Y') }}</p>
                            <p class="text-yellow-300 font-semibold mt-2 text-sm">{{ str_repeat('★', $trip->rating) }}</p>
                        </div>
                    </div>

                    <!-- Badge -->
                    <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold">View</span>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-300">
            <div class="text-6xl mb-4">📷</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No photos yet</h3>
            <p class="text-gray-600 mb-6">Add trips with photos to build your gallery</p>
            <a href="{{ url('/map') }}" class="px-6 py-3 gradient-primary text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                Start Adding Memories
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
