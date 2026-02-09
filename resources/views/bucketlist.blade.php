@extends('layout')

<style>
    /* Horizontal scroll styling */
    #bucketGrid::-webkit-scrollbar {
        height: 6px;
    }
    #bucketGrid::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    #bucketGrid::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    #bucketGrid::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

@section('content')
<div class="min-h-screen bg-gray-50 p-6">

    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Bucket List</h1>
                <p class="text-gray-600">{{ count($buckets) }} destination(s) to explore</p>
            </div>
            <button 
                onclick="openAddModal()"
                class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                + Add Destination
            </button>
        </div>

        <!-- Filter by Priority -->
        <div class="flex gap-2">
            <button onclick="filterByPriority('all')" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-full text-sm font-semibold hover:bg-gray-400 transition filterBtn" id="filterAllBtn">
                All ({{ count($buckets) }})
            </button>
            <button onclick="filterByPriority('high')" class="px-4 py-2 bg-red-500 text-white rounded-full text-sm font-semibold hover:bg-red-600 transition filterBtn" id="filterHighBtn">
                <i class="fas fa-circle text-xs"></i> High ({{ count(array_filter($buckets->toArray(), fn($b) => $b['priority'] == 'high')) }})
            </button>
            <button onclick="filterByPriority('medium')" class="px-4 py-2 bg-yellow-400 text-gray-800 rounded-full text-sm font-semibold hover:bg-yellow-500 transition filterBtn" id="filterMediumBtn">
                <i class="fas fa-circle text-xs"></i> Medium ({{ count(array_filter($buckets->toArray(), fn($b) => $b['priority'] == 'medium')) }})
            </button>
            <button onclick="filterByPriority('low')" class="px-4 py-2 bg-green-500 text-white rounded-full text-sm font-semibold hover:bg-green-600 transition filterBtn" id="filterLowBtn">
                <i class="fas fa-circle text-xs"></i> Low ({{ count(array_filter($buckets->toArray(), fn($b) => $b['priority'] == 'low')) }})
            </button>
        </div>
    </div>


    <div class="max-w-7xl mx-auto">
        <div class="flex gap-6 overflow-x-auto pb-4 snap-x snap-mandatory" id="bucketGrid">
            
            @forelse($buckets as $bucket)
            <div class="bucketItem bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all border border-gray-100 flex-shrink-0 w-80 snap-center" data-priority="{{ $bucket->priority }}">
                    
                    <div class="mb-3">
                        <h3 class="text-xl font-bold text-gray-900">
                            {{ $bucket->destination }}
                        </h3>
                    </div>
               
                    <p class="text-sm text-gray-600 mb-4 min-h-[40px] line-clamp-3">
                        {{ $bucket->description ?? 'No description provided' }}
                    </p>

                    <div class="flex justify-between items-center gap-4 pt-4 border-t border-gray-100">
         
                        <span class="text-xs font-bold px-3 py-1.5 rounded-full whitespace-nowrap
                            {{ $bucket->priority == 'high' ? 'bg-gradient-to-r from-red-500 to-pink-500 text-white shadow-sm' :
                               ($bucket->priority == 'medium' ? 'bg-gradient-to-r from-yellow-400 to-orange-400 text-white shadow-sm' :
                               'bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-sm') }}">
                            {{ ucfirst($bucket->priority) }}
                        </span>

             
                        <div class="flex gap-4 items-center">
                          
                            <button 
                                onclick='openEditModal(@json($bucket))'
                                class="text-blue-600 hover:text-blue-700 font-semibold text-sm transition-colors whitespace-nowrap">
                                Edit
                            </button>

                           
                            <form 
                                action="{{ route('bucket.destroy', $bucket->id) }}" 
                                method="POST" 
                                class="inline m-0">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit"
                                    onclick="return confirm('Are you sure you want to delete {{ $bucket->destination }}?')"
                                    class="text-red-600 hover:text-red-700 font-semibold text-sm transition-colors whitespace-nowrap">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
            </div>
            @empty
          
                <div class="w-full bg-white rounded-2xl border border-gray-100 py-16 text-center shadow-sm">
                    <div class="mb-4 flex justify-center">
                        <svg class="w-20 h-20 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No destinations yet</h3>
                    <p class="text-gray-600 mb-6">Start building your travel bucket list</p>
                    <button 
                        onclick="openAddModal()"
                        class="inline-block px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all">
                        Add Your First Destination
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>


<div 
    id="modal" 
    class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
    onclick="handleBackdropClick(event)">
    
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        
       
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white rounded-t-2xl">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-900">
                Add Destination
            </h2>
        </div>
        <form id="bucketForm" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="_method" id="methodField">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Destination <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="destination" 
                        id="destination"
                        placeholder="Search and select a place..."
                        onkeyup="handleSearchKeyup()"
                        onkeypress="if(event.key === 'Enter') searchPlace()"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" 
                        required>
                    
                    <!-- Search Suggestions Dropdown -->
                    <div id="suggestionsDropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-xl shadow-lg z-50 max-h-64 overflow-y-auto">
                        <ul id="suggestionsList" class="py-2"></ul>
                    </div>
                </div>
                <p id="searchStatus" class="text-xs text-red-500 mt-1 hidden">Place not found.</p>
            </div>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Description
                </label>
                <textarea 
                    name="description" 
                    id="description"
                    rows="3"
                    placeholder="What makes this destination special?"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none outline-none"></textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Priority Level
                </label>
                <select 
                    name="priority" 
                    id="priority"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none">
                    <option value="low">● Low - Someday maybe</option>
                    <option value="medium" selected>● Medium - Next year or two</option>
                    <option value="high">● High - Must visit soon!</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button 
                    type="button" 
                    onclick="closeModal()"
                    class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition-all">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let searchTimeout;
    const destination = document.getElementById('destination');
    const dropdown = document.getElementById('suggestionsDropdown');
    const suggestionsList = document.getElementById('suggestionsList');

    function handleSearchKeyup() {
        const query = destination.value.trim();
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchSuggestions(query);
        }, 300);
    }

    async function fetchSuggestions(query) {
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

            if (data.length === 0) {
                document.getElementById('searchStatus').classList.remove('hidden');
                dropdown.classList.add('hidden');
                return;
            }

            document.getElementById('searchStatus').classList.add('hidden');

            const filtered = data.filter(place => place.importance > 0.1).sort((a, b) => b.importance - a.importance);

            filtered.forEach(place => {
                const li = document.createElement('li');
                const address = place.address || {};
                
                let locationStr = '';
                if (address.city) locationStr = address.city;
                else if (address.town) locationStr = address.town;
                else if (address.village) locationStr = address.village;
                
                if (address.country) {
                    locationStr = locationStr ? `${locationStr}, ${address.country}` : address.country;
                }
                
                const displayName = place.display_name.split(',')[0];
                
                li.innerHTML = `
                    <button type="button" onclick="selectSuggestion({name: '${displayName.replace(/'/g, "\\'")}', lat: ${place.lat}, lon: ${place.lon}})" 
                            class="w-full text-left px-4 py-2 hover:bg-blue-50 transition-colors border-b border-gray-100 last:border-b-0">
                        <div class="font-semibold text-gray-900">${displayName}</div>
                        <div class="text-xs text-gray-500">${locationStr || 'Location'}</div>
                    </button>
                `;
                suggestionsList.appendChild(li);
            });

            dropdown.classList.remove('hidden');
        } catch (error) {
            console.error('Search error:', error);
            document.getElementById('searchStatus').classList.remove('hidden');
        }
    }

    function selectSuggestion(place) {
        destination.value = place.name;
        document.getElementById('latitude').value = place.lat;
        document.getElementById('longitude').value = place.lon;
        dropdown.classList.add('hidden');
    }

    async function searchPlace() {
        const query = destination.value.trim();
        if (query.length === 0) return;

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
                selectSuggestion({name: data[0].display_name.split(',')[0], lat: data[0].lat, lon: data[0].lon});
            } else {
                document.getElementById('searchStatus').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    function openAddModal() {
        const modal = document.getElementById('modal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('bucketForm');
        const methodField = document.getElementById('methodField');
        
        modal.classList.remove('hidden');
        modalTitle.innerText = 'Add Destination';
        form.action = "{{ route('bucket.store') }}";
        methodField.value = '';

        document.getElementById('destination').value = '';
        document.getElementById('description').value = '';
        document.getElementById('priority').value = 'medium';
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        dropdown.classList.add('hidden');
      
        setTimeout(() => {
            document.getElementById('destination').focus();
        }, 100);
    }

    function openEditModal(bucket) {
        const modal = document.getElementById('modal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('bucketForm');
        const methodField = document.getElementById('methodField');
        
        modal.classList.remove('hidden');
        modalTitle.innerText = 'Edit Destination';
        form.action = `/bucket/${bucket.id}`;
        methodField.value = 'PUT';

        document.getElementById('destination').value = bucket.destination;
        document.getElementById('description').value = bucket.description || '';
        document.getElementById('priority').value = bucket.priority;
        document.getElementById('latitude').value = bucket.latitude || '';
        document.getElementById('longitude').value = bucket.longitude || '';
        dropdown.classList.add('hidden');

        setTimeout(() => {
            document.getElementById('destination').focus();
        }, 100);
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }

    function handleBackdropClick(event) {
        if (event.target.id === 'modal') {
            closeModal();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!destination.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Filter by priority
    function filterByPriority(priority) {
        const items = document.querySelectorAll('.bucketItem');
        items.forEach(item => {
            if (priority === 'all' || item.dataset.priority === priority) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });

        // Update button styles
        document.getElementById('filterAllBtn').className = priority === 'all' ? 'px-4 py-2 bg-gray-600 text-white rounded-full text-sm font-semibold hover:bg-gray-700 transition filterBtn' : 'px-4 py-2 bg-gray-300 text-gray-800 rounded-full text-sm font-semibold hover:bg-gray-400 transition filterBtn';
        document.getElementById('filterHighBtn').className = priority === 'high' ? 'px-4 py-2 bg-red-600 text-white rounded-full text-sm font-semibold hover:bg-red-700 transition filterBtn' : 'px-4 py-2 bg-red-500 text-white rounded-full text-sm font-semibold hover:bg-red-600 transition filterBtn';
        document.getElementById('filterMediumBtn').className = priority === 'medium' ? 'px-4 py-2 bg-yellow-500 text-gray-800 rounded-full text-sm font-semibold hover:bg-yellow-600 transition filterBtn' : 'px-4 py-2 bg-yellow-400 text-gray-800 rounded-full text-sm font-semibold hover:bg-yellow-500 transition filterBtn';
        document.getElementById('filterLowBtn').className = priority === 'low' ? 'px-4 py-2 bg-green-600 text-white rounded-full text-sm font-semibold hover:bg-green-700 transition filterBtn' : 'px-4 py-2 bg-green-500 text-white rounded-full text-sm font-semibold hover:bg-green-600 transition filterBtn';
    }
</script>
@endsection