@extends('layout')

@section('content')
<div class="h-full w-full flex bg-gray-50">
    
    <div class="flex-1 relative">
        <div id="map" class="h-full w-full z-0"></div>
        
        <div id="loader" class="hidden absolute inset-0 bg-white/50 z-[999] flex items-center justify-center">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
        </div>
    </div>

    <div class="w-96 bg-white border-l border-gray-200 flex flex-col h-full overflow-hidden shadow-2xl z-10">
        
        <div class="p-6 bg-gradient-to-r from-blue-600 to-cyan-600 text-white">
            <h1 class="text-2xl font-bold">Travel Journal</h1>
            <div class="flex gap-2 mt-4">
                <button onclick="switchView('trips')" id="tripsBtn" class="flex-1 px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold transition-all">
                    <i class="fas fa-map-marker-alt mr-1"></i> Trips
                </button>
                <button onclick="switchView('buckets')" id="bucketsBtn" class="flex-1 px-4 py-2 bg-blue-500 bg-opacity-40 text-white rounded-lg font-semibold transition-all hover:bg-opacity-60">
                    <i class="fas fa-list-check mr-1"></i> Bucket List
                </button>
            </div>
        </div>

        <!-- Search Section (Trips View) -->
        <div id="tripsSection" class="p-4 border-b bg-gray-50">
            <label class="text-xs font-bold text-gray-500 uppercase">Find a Place</label>
            <div class="relative mt-1">
                <div class="flex gap-2">
                    <input type="text" id="searchInput" placeholder="e.g. Boracay, Paris..." 
                        class="w-full p-2 border rounded text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        onkeypress="handleEnter(event)"
                        onkeyup="handleSearchKeyup()"
                        autocomplete="off">
                    <button onclick="searchPlace()" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                    </button>
                </div>
                <!-- Suggestions Dropdown -->
                <div id="suggestionsDropdown" class="absolute top-full left-0 right-0 mt-1 bg-white border rounded shadow-lg max-h-64 overflow-y-auto z-50 hidden">
                    <div id="suggestionsList" class="divide-y"></div>
                </div>
            </div>
            <p id="searchStatus" class="text-xs text-red-500 mt-1 hidden">Place not found.</p>
        </div>

        <!-- Filter Section (Bucket List View) -->
        <div id="bucketsSection" class="hidden p-4 border-b bg-gray-50">
            <label class="text-xs font-bold text-gray-500 uppercase">Filter by Priority</label>
            <div class="flex gap-2 mt-2">
                <button onclick="filterBuckets('all')" class="px-3 py-1 bg-gray-300 text-gray-800 rounded-full text-xs font-semibold hover:bg-gray-400 transition" id="filterAll">
                    All
                </button>
                <button onclick="filterBuckets('high')" class="px-3 py-1 bg-red-500 text-white rounded-full text-xs font-semibold hover:bg-red-600 transition" id="filterHigh">
                    <i class="fas fa-circle text-xs"></i> High
                </button>
                <button onclick="filterBuckets('medium')" class="px-3 py-1 bg-yellow-400 text-gray-800 rounded-full text-xs font-semibold hover:bg-yellow-500 transition" id="filterMedium">
                    <i class="fas fa-circle text-xs"></i> Medium
                </button>
                <button onclick="filterBuckets('low')" class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold hover:bg-green-600 transition" id="filterLow">
                    <i class="fas fa-circle text-xs"></i> Low
                </button>
            </div>
        </div>

        <div id="addForm" class="hidden flex-col flex-1 bg-white animate-slide">
            <div class="p-4 border-b bg-blue-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">New Trip Details</h3>
                <button onclick="cancelAdd()" class="text-gray-400 hover:text-red-500">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('trip.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-3 overflow-y-auto">
                @csrf
                <input type="hidden" name="latitude" id="lat">
                <input type="hidden" name="longitude" id="lng">

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase">Destination</label>
                    <input type="text" name="destination" id="destInput" class="w-full p-2 border rounded bg-gray-100" readonly>
                </div>

                <div class="flex gap-2">
                    <div class="w-1/2">
                        <label class="block text-xs font-bold text-gray-700 uppercase">Date</label>
                        <input type="date" name="date" class="w-full p-2 border rounded" required>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-xs font-bold text-gray-700 uppercase">Rating</label>
                        <select name="rating" class="w-full p-2 border rounded">
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★</option>
                            <option value="3">★★★</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase">Photo</label>
                    <input type="file" name="image" class="w-full text-sm" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase">Memory</label>
                    <textarea name="description" class="w-full p-2 border rounded h-20" placeholder="Write something..."></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700 shadow-md">
                    Save Memory
                </button>
            </form>
        </div>

        <!-- Trips List -->
        <div id="tripsList" class="flex-1 overflow-y-auto p-4 space-y-4">
            @if($trips->isEmpty())
                <div class="text-center text-gray-400 mt-10">
                    <div class="flex justify-center mb-2">
                        <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <p>Search a place to start!</p>
                </div>
            @else
                @foreach($trips as $trip)
                <div onclick="flyToLocation({{ $trip->latitude }}, {{ $trip->longitude }})" 
                     class="flex gap-3 p-3 border rounded-lg hover:bg-blue-50 cursor-pointer transition bg-white shadow-sm">
                    <div class="h-16 w-16 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                        @if($trip->image)
                            <img src="{{ asset($trip->image) }}" class="h-full w-full object-cover">
                        @else
                            <span class="flex items-center justify-center h-full">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="font-bold text-gray-800 truncate">{{ $trip->destination }}</h4>
                        <p class="text-xs text-gray-500">{{ $trip->date }}</p>
                        <p class="text-yellow-500 text-xs">{{ str_repeat('★', $trip->rating) }}</p>
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <!-- Bucket List -->
        <div id="bucketsList" class="hidden flex-1 overflow-y-auto">
            @if($buckets->isEmpty())
                <div class="text-center text-gray-400 h-full flex flex-col items-center justify-center px-6">
                    <div class="flex justify-center mb-3">
                        <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <p class="font-medium">No destinations yet!</p>
                    <p class="text-xs mt-1">Add items to your bucket list</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($buckets as $bucket)
                    <div class="p-5 hover:bg-blue-50 transition-colors cursor-pointer border-l-4
                        @if($bucket->priority == 'high') border-l-red-500 bg-red-50/30
                        @elseif($bucket->priority == 'medium') border-l-yellow-400 bg-yellow-50/30
                        @else border-l-green-500 bg-green-50/30 @endif"
                         onclick="flyToLocation({{ $bucket->latitude ?? '12.8797' }}, {{ $bucket->longitude ?? '121.7740' }})">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <h4 class="font-bold text-gray-900 text-sm truncate flex-1">{{ $bucket->destination }}</h4>
                            <span class="px-2 py-1 rounded text-xs font-bold flex-shrink-0 whitespace-nowrap
                                @if($bucket->priority == 'high') bg-red-500 text-white
                                @elseif($bucket->priority == 'medium') bg-yellow-400 text-gray-800
                                @else bg-green-500 text-white @endif">
                                {{ ucfirst($bucket->priority) }}
                            </span>
                        </div>
                        @if($bucket->description)
                            <p class="text-xs text-gray-600 line-clamp-2">{{ $bucket->description }}</p>
                        @else
                            <p class="text-xs text-gray-400 italic">No description</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // 1. Initialize Map
        var map = L.map('map').setView([12.8797, 121.7740], 6); // Philippines Center
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var tempMarker = null;
        var formDiv = document.getElementById('addForm');
        var tripsList = document.getElementById('tripsList');
        var bucketsList = document.getElementById('bucketsList');
        var currentView = 'trips';
        var currentFilter = 'all';
        var tripMarkers = {};
        var bucketMarkers = {};

        // Get data from PHP
        var trips = @json($trips);
        var buckets = @json($buckets);

        // Priority colors for buckets
        const priorityColors = {
            'high': { color: '#EF4444', icon: '🔴' },    // Red
            'medium': { color: '#FBBF24', icon: '🟡' },  // Yellow
            'low': { color: '#10B981', icon: '🟢' }      // Green
        };

        // Add trip markers to map
        trips.forEach(trip => {
            const marker = L.marker([trip.latitude, trip.longitude]).addTo(map)
                .bindPopup(`<b>${trip.destination}</b>`);
            tripMarkers[trip.id] = marker;
        });

        // Add bucket list markers to map
        function loadBucketMarkers() {
            buckets.forEach(bucket => {
                if (bucket.latitude && bucket.longitude) {
                    const color = priorityColors[bucket.priority];
                    const icon = L.icon({
                        iconUrl: `data:image/svg+xml;base64,${btoa(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${color.color}"><path d="M12 0C7.58 0 4 3.58 4 8c0 2.24.96 4.28 2.5 5.73V22h11V13.73c1.54-1.45 2.5-3.49 2.5-5.73 0-4.42-3.58-8-8-8z"/></svg>`)}`,
                        iconSize: [32, 40],
                        iconAnchor: [16, 40],
                        popupAnchor: [0, -40]
                    });
                    const marker = L.marker([bucket.latitude, bucket.longitude], { icon: icon }).addTo(map)
                        .bindPopup(`<b>${bucket.destination}</b><br/><span class="text-xs">Priority: ${bucket.priority}</span>`);
                    marker.setOpacity(0); // Hidden by default - show only when bucket list view is active
                    bucketMarkers[bucket.id] = marker;
                }
            });
        }

        loadBucketMarkers();

        // Switch between views
        function switchView(view) {
            currentView = view;
            currentFilter = 'all';
            
            // Update buttons
            if (view === 'trips') {
                document.getElementById('tripsBtn').classList.add('bg-white', 'text-blue-600');
                document.getElementById('tripsBtn').classList.remove('bg-blue-500', 'bg-opacity-40', 'text-white');
                document.getElementById('bucketsBtn').classList.remove('bg-white', 'text-blue-600');
                document.getElementById('bucketsBtn').classList.add('bg-blue-500', 'bg-opacity-40', 'text-white');
                
                document.getElementById('tripsSection').classList.remove('hidden');
                document.getElementById('bucketsSection').classList.add('hidden');
                document.getElementById('tripsList').classList.remove('hidden');
                document.getElementById('bucketsList').classList.add('hidden');
                document.getElementById('addForm').classList.add('hidden');
                
                // Show trip markers, hide bucket markers
                trips.forEach(trip => {
                    if (tripMarkers[trip.id]) tripMarkers[trip.id].setOpacity(1);
                });
                buckets.forEach(bucket => {
                    if (bucketMarkers[bucket.id]) bucketMarkers[bucket.id].setOpacity(0);
                });
            } else {
                document.getElementById('bucketsBtn').classList.add('bg-white', 'text-blue-600');
                document.getElementById('bucketsBtn').classList.remove('bg-blue-500', 'bg-opacity-40', 'text-white');
                document.getElementById('tripsBtn').classList.remove('bg-white', 'text-blue-600');
                document.getElementById('tripsBtn').classList.add('bg-blue-500', 'bg-opacity-40', 'text-white');
                
                document.getElementById('bucketsSection').classList.remove('hidden');
                document.getElementById('tripsSection').classList.add('hidden');
                document.getElementById('bucketsList').classList.remove('hidden');
                document.getElementById('tripsList').classList.add('hidden');
                document.getElementById('addForm').classList.add('hidden');
                
                // Show bucket markers, hide trip markers
                buckets.forEach(bucket => {
                    if (bucketMarkers[bucket.id]) bucketMarkers[bucket.id].setOpacity(1);
                });
                trips.forEach(trip => {
                    if (tripMarkers[trip.id]) tripMarkers[trip.id].setOpacity(0);
                });
            }
        }

        // Filter bucket list by priority
        function filterBuckets(priority) {
            currentFilter = priority;
            const bucket_items = document.getElementById('bucketsList').querySelectorAll('[class*="p-3"]');
            
            bucket_items.forEach(item => {
                const itemHTML = item.innerHTML;
                const isPriorityShown = priority === 'all' || itemHTML.includes(priority.charAt(0).toUpperCase() + priority.slice(1));
                item.style.display = isPriorityShown ? 'block' : 'none';
            });

            // Update filter button styles
            document.getElementById('filterAll').className = priority === 'all' ? 'px-3 py-1 bg-gray-500 text-white rounded-full text-xs font-semibold hover:bg-gray-600 transition' : 'px-3 py-1 bg-gray-300 text-gray-800 rounded-full text-xs font-semibold hover:bg-gray-400 transition';
            document.getElementById('filterHigh').className = priority === 'high' ? 'px-3 py-1 bg-red-600 text-white rounded-full text-xs font-semibold hover:bg-red-700 transition' : 'px-3 py-1 bg-red-500 text-white rounded-full text-xs font-semibold hover:bg-red-600 transition';
            document.getElementById('filterMedium').className = priority === 'medium' ? 'px-3 py-1 bg-yellow-500 text-gray-800 rounded-full text-xs font-semibold hover:bg-yellow-600 transition' : 'px-3 py-1 bg-yellow-400 text-gray-800 rounded-full text-xs font-semibold hover:bg-yellow-500 transition';
            document.getElementById('filterLow').className = priority === 'low' ? 'px-3 py-1 bg-green-600 text-white rounded-full text-xs font-semibold hover:bg-green-700 transition' : 'px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold hover:bg-green-600 transition';

            // Also filter markers on map
            buckets.forEach(bucket => {
                if (bucketMarkers[bucket.id]) {
                    const show = priority === 'all' || bucket.priority === priority;
                    bucketMarkers[bucket.id].setOpacity(show ? 1 : 0.2);
                }
            });
        }

        // 3. THE SEARCH FUNCTION (Replaces Google Autocomplete)
        let searchTimeout;
        
        function handleSearchKeyup() {
            clearTimeout(searchTimeout);
            var query = document.getElementById('searchInput').value;
            var dropdown = document.getElementById('suggestionsDropdown');
            
            if (query.length < 2) {
                dropdown.classList.add('hidden');
                return;
            }
            
            // Debounce: wait 300ms after user stops typing
            searchTimeout = setTimeout(() => {
                fetchSuggestions(query);
            }, 300);
        }
        
        async function fetchSuggestions(query) {
            var dropdown = document.getElementById('suggestionsDropdown');
            var suggestionsList = document.getElementById('suggestionsList');
            
            try {
                const params = new URLSearchParams({
                    q: query,
                    format: 'json',
                    limit: 8,
                    dedupe: 1,
                    addressdetails: 1,
                    extratags: 1
                });
                
                const response = await fetch(`https://nominatim.openstreetmap.org/search?${params}`);
                const data = await response.json();

                suggestionsList.innerHTML = '';
                
                if (data.length > 0) {
                    const filtered = data.filter(place => place.importance > 0.1).sort((a, b) => b.importance - a.importance);
                    
                    filtered.forEach((place) => {
                        const name = place.display_name.split(',')[0];
                        const address = place.address || {};
                        
                        let locationStr = '';
                        if (address.city) locationStr = address.city;
                        else if (address.town) locationStr = address.town;
                        else if (address.village) locationStr = address.village;
                        
                        if (address.country) {
                            locationStr = locationStr ? `${locationStr}, ${address.country}` : address.country;
                        }
                        
                        const suggestionDiv = document.createElement('div');
                        suggestionDiv.className = 'p-3 hover:bg-blue-50 cursor-pointer transition text-sm border-b border-gray-100 last:border-b-0';
                        suggestionDiv.innerHTML = `
                            <div class="font-semibold text-gray-900">${name}</div>
                            <div class="text-xs text-gray-500">${locationStr || 'Location'}</div>
                        `;
                        suggestionDiv.onclick = () => selectSuggestion(place);
                        suggestionsList.appendChild(suggestionDiv);
                    });
                    dropdown.classList.remove('hidden');
                } else {
                    dropdown.classList.add('hidden');
                }
            } catch (error) {
                console.error("Search failed", error);
                dropdown.classList.add('hidden');
            }
        }
        
        function selectSuggestion(place) {
            var lat = place.lat;
            var lon = place.lon;
            var name = place.display_name.split(',')[0];
            
            // Update search input
            document.getElementById('searchInput').value = name;
            
            // Hide dropdown
            document.getElementById('suggestionsDropdown').classList.add('hidden');
            
            // Update Map
            map.flyTo([lat, lon], 12);
            placeTempMarker(lat, lon, name);
        }
        
        async function searchPlace() {
            var query = document.getElementById('searchInput').value;
            var statusMsg = document.getElementById('searchStatus');
            var loader = document.getElementById('loader');

            if(!query) return;

            // Show loading
            loader.classList.remove('hidden');
            statusMsg.classList.add('hidden');

            // Call Nominatim API with enhanced parameters
            try {
                const params = new URLSearchParams({
                    q: query,
                    format: 'json',
                    dedupe: 1,
                    addressdetails: 1,
                    extratags: 1
                });
                
                const response = await fetch(`https://nominatim.openstreetmap.org/search?${params}`);
                const data = await response.json();

                if (data.length > 0) {
                    selectSuggestion(data[0]);
                } else {
                    statusMsg.classList.remove('hidden');
                }
            } catch (error) {
                console.error("Search failed", error);
                statusMsg.innerText = "Error searching.";
                statusMsg.classList.remove('hidden');
            } finally {
                loader.classList.add('hidden');
            }
        }

        // 4. Place Marker & Show Form
        function placeTempMarker(lat, lng, name) {
            // Remove old temp marker
            if (tempMarker) map.removeLayer(tempMarker);

            // Add new marker (Red)
            tempMarker = L.marker([lat, lng]).addTo(map);
            
            // Fill Form
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            document.getElementById('destInput').value = name; // Auto-fill name

            // Swap Sidebar View (Hide List, Show Form)
            tripsList.classList.add('hidden');
            formDiv.classList.remove('hidden');
        }

        // 5. Handle "Enter" key in search box
        function handleEnter(e) {
            if (e.key === 'Enter') searchPlace();
        }

        // 6. Cancel / Reset View
        function cancelAdd() {
            formDiv.classList.add('hidden');
            tripsList.classList.remove('hidden');
            if (tempMarker) map.removeLayer(tempMarker);
            document.getElementById('searchInput').value = '';
            map.setZoom(6); // Zoom back out
        }

        // 7. Click Map to Pin Manually (Optional backup)
        map.on('click', function(e) {
            if (currentView === 'trips') {
                placeTempMarker(e.latlng.lat, e.latlng.lng, "Pinned Location");
            }
        });

        function flyToLocation(lat, lng) {
            map.flyTo([lat, lng], 12);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            var searchInput = document.getElementById('searchInput');
            var dropdown = document.getElementById('suggestionsDropdown');
            
            if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
@endsection