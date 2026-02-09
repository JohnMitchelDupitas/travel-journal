@extends('layout')

@section('content')
<div class="h-full w-full flex bg-gray-50">
    <!-- Map Section -->
    <div class="flex-1 relative">
        <div id="map" class="h-full w-full z-0"></div>
        
        <!-- Instruction Box -->
        <div class="absolute top-6 left-6 z-[999] bg-white rounded-2xl shadow-lg p-5 max-w-sm card-shadow border border-gray-200 animate-slide">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/>
                </svg>
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
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                </svg> Log a New Trip
            </h3>
            <form action="{{ route('trip.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="latitude" id="lat">
                <input type="hidden" name="longitude" id="lng">

                <!-- Destination -->
                <div>
                    <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">
                        <svg class="w-4 h-4 text-red-500 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/>
                        </svg> Destination
                    </label>
                    <input type="text" name="destination" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition text-sm" placeholder="e.g. Mount Pinatubo" required>
                </div>

                <!-- Date & Rating Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">
                            <svg class="w-4 h-4 text-amber-600 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zm-5-7h-4v4h4v-4z"/>
                            </svg> Date
                        </label>
                        <input type="date" name="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">
                            <svg class="w-4 h-4 text-yellow-500 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2l-2.81 6.63L2 9.24l5.46 4.73L5.82 21 12 17.27z"/>
                            </svg> Rating
                        </label>
                        <select name="rating" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition text-sm">
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★</option>
                            <option value="3">★★★</option>
                            <option value="2">★★</option>
                            <option value="1">★</option>
                        </select>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div>
                    <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">
                        <svg class="w-4 h-4 text-purple-600 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                        </svg> Photo
                    </label>
                    <div class="relative">
                        <input type="file" name="image" class="w-full file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-500 file:text-white file:font-semibold hover:file:bg-blue-600 text-sm cursor-pointer" required>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold text-gray-900 uppercase mb-2 tracking-wide">
                        <svg class="w-4 h-4 text-pink-500 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg> Memory
                    </label>
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
        
        <!-- Trips Timeline (Scrollable) -->
        <div class="flex-1 overflow-y-auto">
            <div id="tripsList" class="divide-y divide-gray-200">
                <!-- Trip cards will be rendered here -->
            </div>
            <div id="noTripsMessage" class="flex flex-col items-center justify-center h-full text-center py-12 px-6">
                <div class="mb-4 flex justify-center">
                    <svg class="w-16 h-16 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <p class="font-bold text-gray-900 text-lg">No trips yet</p>
                <p class="text-gray-600 text-sm mt-2">Click on the map to start logging your travel memories!</p>
            </div>
        </div>
    </div>
</div>

    </div>
</div>
@endsection

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
        var tripsList = document.getElementById('tripsList');
        var noTripsMessage = document.getElementById('noTripsMessage');

        // Render trip cards in sidebar
        function renderTripCards() {
            tripsList.innerHTML = '';
            if (trips.length === 0) {
                noTripsMessage.style.display = 'flex';
                return;
            }
            noTripsMessage.style.display = 'none';
            
            trips.forEach(trip => {
                const tripCard = document.createElement('div');
                tripCard.className = 'p-5 hover:bg-blue-50 transition cursor-pointer trip-item';
                tripCard.onclick = () => flyToLocation(trip.latitude, trip.longitude);
                
                tripCard.innerHTML = `
                    <div class="flex gap-4">
                        ${trip.image ? `<img src="{{ asset('') }}${trip.image}" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">` : '<div class="w-20 h-20 bg-gray-200 rounded-lg flex-shrink-0 flex items-center justify-center"><svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/></svg></div>'}
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900 truncate">${trip.destination}</p>
                            <p class="text-xs text-gray-500 mt-1">${trip.date}</p>
                            <p class="text-yellow-500 font-bold text-xs mt-1">${'★'.repeat(trip.rating)}</p>
                            ${trip.description ? `<p class="text-xs text-gray-600 mt-2 line-clamp-2">${trip.description}</p>` : ''}
                        </div>
                    </div>
                `;
                tripsList.appendChild(tripCard);
            });
        }

        // Load existing trip markers
        trips.forEach(trip => {
            var marker = L.marker([trip.latitude, trip.longitude]).addTo(map);
            
            var imageSrc = trip.image ? '{{ asset("") }}' + trip.image : '';
            var popupContent = `
                <div class="text-center">
                    <b class="block text-lg text-gray-900 mb-2">${trip.destination}</b>
                    ${imageSrc ? `<img src="${imageSrc}" style="width:140px; height:100px; object-fit:cover; margin:5px auto; border-radius:6px; display:block;">` : '<div style="width:140px; height:100px; margin:5px auto; border-radius:6px; display:flex; align-items:center; justify-content:center; background:#f0f0f0;"><svg style="width:30px;height:30px;fill:#ef4444;" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/></svg></div>'}
                    <br>
                    <span class="text-yellow-500 font-bold text-sm">${'★'.repeat(trip.rating)}</span>
                    <br>
                    <small class="text-gray-600" style="display:block; margin-top: 8px;">${trip.date}</small>
                    ${trip.description ? `<small class="text-gray-600" style="display:block; margin-top: 4px;">${trip.description}</small>` : ''}
                </div>
            `;
            
            marker.bindPopup(popupContent);
        });
        
        // Render trip cards
        renderTripCards();

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