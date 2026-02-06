@extends('layout')

@section('content')
<div class="h-full w-full flex bg-gray-50">
    <!-- Map Section -->
    <div class="flex-1 relative">
        <div id="map" class="h-full w-full z-0"></div>
        
        <!-- Instruction Box -->
        <div class="absolute top-6 left-6 z-[999] bg-white rounded-2xl shadow-lg p-5 max-w-sm card-shadow border border-gray-200 animate-slide">
            <div class="flex items-start gap-3">
                <span class="text-2xl">📍</span>
                <div>
                    <p class="font-bold text-gray-900">Add a New Trip</p>
                    <p class="text-sm text-gray-600 mt-1">Click anywhere on the map to log your destination</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar with Trips List -->
    <div class="w-96 bg-white border-l border-gray-200 flex flex-col h-full overflow-hidden card-shadow">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-cyan-600">
            <h1 class="text-2xl font-bold text-white">Travel Journal</h1>
            <p class="text-blue-100 text-sm mt-2 flex items-center gap-1">
                 Collect memories, not things
            </p>
        </div>

        <!-- Add Trip Form (Hidden by default) -->
        <div id="addForm" class="hidden p-6 bg-gradient-to-br from-blue-50 to-cyan-50 border-b border-gray-200 transition-all max-h-[60vh] overflow-y-auto animate-slide">
            <h3 class="font-bold text-gray-900 mb-4 text-lg flex items-center gap-2">
                <span>📝</span> Log a New Trip
            </h3>
            <form action="{{ route('trip.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="latitude" id="lat">
                <input type="hidden" name="longitude" id="lng">

                <!-- Destination -->
                <div>
                    <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">📍 Destination</label>
                    <input type="text" name="destination" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition text-sm" placeholder="e.g. Mount Pinatubo" required>
                </div>

                <!-- Date & Rating Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">📅 Date</label>
                        <input type="date" name="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">⭐ Rating</label>
                        <select name="rating" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition text-sm">
                            <option value="5">⭐⭐⭐⭐⭐</option>
                            <option value="4">⭐⭐⭐⭐</option>
                            <option value="3">⭐⭐⭐</option>
                            <option value="2">⭐⭐</option>
                            <option value="1">⭐</option>
                        </select>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div>
                    <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">📷 Photo</label>
                    <div class="relative">
                        <input type="file" name="image" class="w-full file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-500 file:text-white file:font-semibold hover:file:bg-blue-600 text-sm cursor-pointer" required>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">💭 Memory</label>
                    <textarea name="description" placeholder="What made this place special?" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition text-sm resize-none" rows="3"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="button" onclick="cancelAdd()" class="px-4 py-3 text-gray-700 border-2 border-gray-200 rounded-lg hover:bg-gray-100 font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-3 gradient-primary text-white rounded-lg font-semibold hover:shadow-lg transition transform hover:scale-105 duration-200">
                        Save Memory
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Section -->
        @if($trips->count() > 0)
        <div class="grid grid-cols-3 gap-3 p-5 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200">
            <div class="text-center p-3 bg-white rounded-lg border border-gray-100">
                <p class="text-2xl font-bold text-blue-600">{{ $trips->count() }}</p>
                <p class="text-xs text-gray-600 mt-1 font-medium">Trips</p>
            </div>
            <div class="text-center p-3 bg-white rounded-lg border border-gray-100">
                <p class="text-2xl font-bold text-amber-500">{{ round($trips->avg('rating'), 1) }}</p>
                <p class="text-xs text-gray-600 mt-1 font-medium">Avg Rating</p>
            </div>
            <div class="text-center p-3 bg-white rounded-lg border border-gray-100">
                <p class="text-2xl font-bold text-green-600">{{ $trips->pluck('destination')->unique()->count() }}</p>
                <p class="text-xs text-gray-600 mt-1 font-medium">Places</p>
            </div>
        </div>
        @endif

        <!-- Trips Timeline (Scrollable) -->
        <div class="flex-1 overflow-y-auto">
            @if($trips->count() > 0)
                <div class="p-4 space-y-3">
                    @foreach($trips as $trip)
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all group overflow-hidden">
                        <!-- Image -->
                        <div class="relative h-28 overflow-hidden bg-gray-300">
                            <img src="{{ asset($trip->image) }}" alt="{{ $trip->destination }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                            <h3 class="absolute bottom-2 left-3 text-white font-bold text-sm group-hover:text-base transition">{{ $trip->destination }}</h3>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-1 rounded">{{ \Carbon\Carbon::parse($trip->date)->format('M d, Y') }}</span>
                                <span class="text-yellow-500 font-bold text-sm">{{ str_repeat('★', $trip->rating) }}</span>
                            </div>
                            
                            <p class="text-gray-700 text-xs line-clamp-2 mb-3">{{ $trip->description ?? 'No description' }}</p>
                            
                            <!-- Actions -->
                            <div class="flex gap-2 pt-3 border-t border-gray-100">
                                <button onclick="flyToLocation({{ $trip->latitude }}, {{ $trip->longitude }})" class="flex-1 text-xs text-blue-600 hover:text-blue-700 font-bold hover:bg-blue-50 py-2 rounded transition">📍 Map</button>
                                <form action="{{ route('trip.destroy', $trip->id) }}" method="POST" class="flex-1">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full text-xs text-red-600 hover:text-red-700 font-bold hover:bg-red-50 py-2 rounded transition" onclick="return confirm('Delete this trip?')">🗑️ Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-center py-12 px-6">
                    <div class="text-5xl mb-4">🗺️</div>
                    <p class="font-bold text-gray-900 text-lg">No trips yet</p>
                    <p class="text-gray-600 text-sm mt-2">Click on the map to start logging your travel memories!</p>
                </div>
            @endif
        </div>
    </div>
</div>

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize Leaflet Map
    var map = L.map('map').setView([12.8797, 121.7740], 6);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    var tempMarker = null;
    var formDiv = document.getElementById('addForm');
    var latInput = document.getElementById('lat');
    var lngInput = document.getElementById('lng');
    var trips = @json($trips);

    // Load existing trip markers
    trips.forEach(trip => {
        var marker = L.marker([trip.latitude, trip.longitude]).addTo(map);
        
        var popupContent = `
            <div class="text-center">
                <b class="block text-lg text-gray-900 mb-2">${trip.destination}</b>
                <img src="${trip.image}" style="width:140px; height:100px; object-fit:cover; margin:5px auto; border-radius:6px; display:block;">
                <br>
                <span class="text-yellow-500 font-bold text-sm">${'★'.repeat(trip.rating)}</span>
                <br>
                <small class="text-gray-600" style="display:block; margin-top: 8px;">${trip.date}</small>
                ${trip.description ? `<small class="text-gray-600" style="display:block; margin-top: 4px;">${trip.description}</small>` : ''}
            </div>
        `;
        
        marker.bindPopup(popupContent);
    });

    // Handle map click to add trip
    map.on('click', function(e) {
        if (tempMarker) map.removeLayer(tempMarker);
        
        tempMarker = L.marker(e.latlng, {opacity: 0.7}).addTo(map);
        tempMarker.setIcon(L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        }));

        latInput.value = e.latlng.lat.toFixed(8);
        lngInput.value = e.latlng.lng.toFixed(8);
        
        formDiv.classList.remove('hidden');
        formDiv.scrollIntoView({behavior: 'smooth'});
    });

    function cancelAdd() {
        formDiv.classList.add('hidden');
        if (tempMarker) map.removeLayer(tempMarker);
    }

    function flyToLocation(lat, lng) {
        map.flyTo([lat, lng], 12, {
            duration: 1.5,
            easeLinearity: 0.25
        });
    }
</script>
@endsection
@endsection


@section('content')
<div class="h-full w-full flex">
    <!-- Map Section -->
    <div class="flex-1 relative">
        <div id="map" class="h-full w-full z-0"></div>
        
        <div class="absolute top-4 left-4 z-[999] bg-white px-4 py-3 rounded-lg shadow-md max-w-xs">
            <p class="text-sm font-semibold text-gray-700">📍 Click on the map to add a new trip!</p>
        </div>
    </div>

    <!-- Sidebar with Trips List -->
    <div class="w-80 bg-white border-l flex flex-col h-full overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <h1 class="text-2xl font-bold">Travel Journal</h1>
            <p class="text-blue-100 text-sm mt-1">Collect memories, not things.</p>
        </div>

        <!-- Add Trip Form (Hidden by default) -->
        <div id="addForm" class="hidden p-6 bg-gray-50 border-b border-gray-200 transition-all max-h-96 overflow-y-auto">
            <h3 class="font-bold text-gray-800 mb-4">Log a New Trip</h3>
            <form action="{{ route('trip.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="latitude" id="lat">
                <input type="hidden" name="longitude" id="lng">

                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Destination</label>
                    <input type="text" name="destination" class="w-full p-2 border rounded text-sm" placeholder="e.g. Mount Pinatubo" required>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Date</label>
                        <input type="date" name="date" class="w-full p-2 border rounded text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Rating</label>
                        <select name="rating" class="w-full p-2 border rounded text-sm">
                            <option value="5">⭐⭐⭐⭐⭐</option>
                            <option value="4">⭐⭐⭐⭐</option>
                            <option value="3">⭐⭐⭐</option>
                            <option value="2">⭐⭐</option>
                            <option value="1">⭐</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Photo</label>
                    <input type="file" name="image" class="w-full text-xs" required>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Description</label>
                    <textarea name="description" placeholder="Write a memory..." class="w-full p-2 border rounded text-sm h-16 resize-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 transition mb-2">Save Memory</button>
                <button type="button" onclick="cancelAdd()" class="w-full text-gray-500 text-sm hover:underline">Cancel</button>
            </form>
        </div>

        <!-- Stats Section -->
        @if($trips->count() > 0)
        <div class="grid grid-cols-3 gap-2 p-4 bg-blue-50 border-b text-center">
            <div>
                <p class="text-lg font-bold text-blue-600">{{ $trips->count() }}</p>
                <p class="text-xs text-gray-600">Trips</p>
            </div>
            <div>
                <p class="text-lg font-bold text-blue-600">{{ round($trips->avg('rating'), 1) }}</p>
                <p class="text-xs text-gray-600">Avg Rating</p>
            </div>
            <div>
                <p class="text-lg font-bold text-blue-600">{{ $trips->pluck('destination')->unique()->count() }}</p>
                <p class="text-xs text-gray-600">Places</p>
            </div>
        </div>
        @endif

        <!-- Trips Timeline (Scrollable) -->
        <div class="flex-1 overflow-y-auto">
            @if($trips->count() > 0)
                <div class="p-4 space-y-4">
                    @foreach($trips as $trip)
                    <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition group overflow-hidden">
                        <!-- Image -->
                        <div class="relative h-24 overflow-hidden bg-gray-200">
                            <img src="{{ asset($trip->image) }}" alt="{{ $trip->destination }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <h3 class="absolute bottom-2 left-2 text-white font-bold text-sm">{{ $trip->destination }}</h3>
                        </div>

                        <!-- Content -->
                        <div class="p-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($trip->date)->format('M d, Y') }}</span>
                                <span class="text-yellow-500 text-xs font-semibold">{{ str_repeat('★', $trip->rating) }}</span>
                            </div>
                            
                            <p class="text-gray-600 text-xs line-clamp-2 mb-2">{{ $trip->description ?? 'No description' }}</p>
                            
                            <!-- Actions -->
                            <div class="flex gap-2 pt-2 border-t">
                                <button onclick="flyToLocation({{ $trip->latitude }}, {{ $trip->longitude }})" class="flex-1 text-xs text-blue-500 hover:underline font-bold">Map</button>
                                <button onclick="openEdit({{ $trip->id }})" class="flex-1 text-xs text-orange-500 hover:underline font-bold">Edit</button>
                                <form action="{{ route('trip.destroy', $trip->id) }}" method="POST" class="inline flex-1">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full text-xs text-red-400 hover:text-red-600 font-bold" onclick="return confirm('Delete this trip?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-400 px-4">
                    <svg class="w-12 h-12 mx-auto mb-2 opacity-30"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                    <p class="font-semibold text-gray-500">No trips yet</p>
                    <p class="text-xs text-gray-400 mt-1">Click the map to start logging memories!</p>
                </div>
            @endif
        </div>
    </div>
</div>

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize Leaflet Map
    var map = L.map('map').setView([12.8797, 121.7740], 6);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    var tempMarker = null;
    var formDiv = document.getElementById('addForm');
    var latInput = document.getElementById('lat');
    var lngInput = document.getElementById('lng');
    var trips = @json($trips);

    // Load existing trip markers
    trips.forEach(trip => {
        var marker = L.marker([trip.latitude, trip.longitude]).addTo(map);
        
        var popupContent = `
            <div class="text-center">
                <b class="block">${trip.destination}</b>
                <img src="${trip.image}" style="width:120px; height:80px; object-fit:cover; margin:5px auto; border-radius:4px; display:block;">
                <small class="block text-yellow-500">${'★'.repeat(trip.rating)}</small>
                <small class="block text-gray-600" style="margin-top:4px;">${trip.date}</small>
            </div>
        `;
        
        marker.bindPopup(popupContent);
    });

    // Handle map click to add trip
    map.on('click', function(e) {
        if (tempMarker) map.removeLayer(tempMarker);
        
        tempMarker = L.marker(e.latlng, {opacity: 0.6, draggable: true}).addTo(map);
        tempMarker.setIcon(L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        }));

        latInput.value = e.latlng.lat.toFixed(8);
        lngInput.value = e.latlng.lng.toFixed(8);
        
        formDiv.classList.remove('hidden');
        formDiv.scrollIntoView({behavior: 'smooth'});
    });

    function cancelAdd() {
        formDiv.classList.add('hidden');
        if (tempMarker) map.removeLayer(tempMarker);
    }

    function flyToLocation(lat, lng) {
        map.flyTo([lat, lng], 12, {duration: 1.5});
    }

    function openEdit(tripId) {
        // Placeholder for edit functionality
        alert('Edit functionality coming soon! Trip ID: ' + tripId);
    }
</script>
@endsection
@endsection
