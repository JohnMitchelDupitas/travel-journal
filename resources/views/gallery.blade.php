
@extends('layout')

<style>
    /* Hide print template from screen */
    #polaroidPrintTemplate {
        display: none;
    }

    @media print {
        @page {
            margin: 0.5in;
            size: A4;
        }

        body, html {
            margin: 0;
            padding: 0;
            background: white;
        }

        /* Hide all except print template */
        aside { display: none !important; }
        .scroll-container { display: none !important; }
        .flex-1 { display: none !important; }

        main {
            display: block !important;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        #polaroidPrintTemplate {
            display: block !important;
        }

        .print-page {
            width: 100%;
            display: block;
            margin: 0;
            padding: 0.5in;
        }

        .polaroid-grid {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr);
            gap: 16pt;
        }

        .polaroid-card {
            display: flex !important;
            flex-direction: column;
            break-inside: avoid;
        }

        .polaroid-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .polaroid-caption {
            padding: 12pt;
            font-family: 'Courier New', monospace;
        }
    }
</style>

@section('content')
<div class="flex-1 overflow-y-auto bg-gradient-to-br from-purple-50 to-blue-50">
    <div class="p-6 md:p-10 max-w-7xl mx-auto">

        <div class="mb-10 text-center flex flex-col items-center">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-2">
                Memory Gallery
            </h1>
            <p class="text-gray-600 mb-6">{{ $trips->count() }} beautiful moments</p>
            @if($trips->count())
            <button type="button" id="printPolaroidsBtn"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-lg font-semibold hover:shadow-lg transition-all flex items-center gap-2">
                <i class="fas fa-print"></i> Print Polaroids
            </button>
            @endif
        </div>

        @if($trips->count())
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($trips as $trip)
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl 
                        transition-all duration-300 hover:-translate-y-1 aspect-square bg-black">

                <img src="{{ asset($trip->image) }}"
                     alt="{{ $trip->destination }}"
                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                <div class="absolute inset-0 bg-gradient-to-t from-black/90 to-transparent opacity-0 
                            group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4">
                    
                    <h3 class="text-white font-bold text-lg mb-1">{{ $trip->destination }}</h3>
                    <p class="text-gray-300 text-xs mb-1">{{ \Carbon\Carbon::parse($trip->date)->format('M Y') }}</p>
                    <p class="text-yellow-400 text-sm mb-4">{{ str_repeat('★', $trip->rating) }}</p>

                    <div class="flex gap-3 justify-center items-stretch">
                        <button type="button" 
                                class="edit-trip-btn flex items-center justify-center gap-2 px-4 py-2.5 h-10 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105 active:scale-95 relative z-10"
                                data-trip-id="{{ $trip->id }}"
                                data-trip-destination="{{ htmlspecialchars($trip->destination, ENT_QUOTES, 'UTF-8') }}"
                                data-trip-date="{{ \Carbon\Carbon::parse($trip->date)->format('Y-m-d') }}"
                                data-trip-rating="{{ $trip->rating }}"
                                data-trip-description="{{ htmlspecialchars($trip->description ?? '', ENT_QUOTES, 'UTF-8') }}"
                                data-trip-image="{{ htmlspecialchars($trip->image ?? '', ENT_QUOTES, 'UTF-8') }}"
                                data-trip-latitude="{{ $trip->latitude }}"
                                data-trip-longitude="{{ $trip->longitude }}">
                            <i class="fas fa-edit text-sm"></i>
                            <span>Edit</span>
                        </button>
                        <form action="{{ route('trip.destroy', $trip->id) }}" method="POST"
                              onsubmit="return confirm('Delete this memory?')" class="flex">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center justify-center gap-2 px-4 py-2.5 h-10 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105 active:scale-95">
                                <i class="fas fa-trash text-sm"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="flex flex-col items-center justify-center py-24 bg-white/80 rounded-2xl border-2 border-dashed border-purple-200">
            <div class="mb-4 flex justify-center">
                <svg class="w-24 h-24 text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Memories Yet</h3>
            <p class="text-gray-600 mb-6">Start capturing your adventures</p>
            <a href="{{ route('map') }}"
               class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl 
                      font-semibold hover:shadow-lg transition transform hover:scale-105">
                Start Your Journey
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Hidden Polaroid Print Template -->
<div id="polaroidPrintTemplate">
    <div class="print-page">
        <div class="polaroid-grid">
            @php
                $latestTrips = $trips->take(9);
            @endphp
            @foreach($latestTrips as $trip)
            <div class="polaroid-card">
                @if($trip->image)
                    <img src="{{ asset($trip->image) }}" alt="{{ $trip->destination }}" class="polaroid-image">
                @else
                    <div class="polaroid-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 50px; height: 50px;" fill="white" viewBox="0 0 24 24">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                        </svg>
                    </div>
                @endif
                <div class="polaroid-caption">
                    <div class="polaroid-location">{{ $trip->destination }}</div>
                    <div class="polaroid-date">{{ \Carbon\Carbon::parse($trip->date)->format('M d, Y') }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-6 text-white rounded-t-2xl relative">
            <button onclick="closeEdit()" class="absolute top-4 right-4 text-white hover:bg-white/20 rounded-full w-8 h-8 flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                </svg>
            </button>
            <h2 class="text-2xl font-bold">Edit Memory</h2>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <!-- Hidden fields for latitude and longitude (required by controller) -->
            <input type="hidden" name="latitude" id="editLatitude">
            <input type="hidden" name="longitude" id="editLongitude">

            <div id="currentImagePreview" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <svg class="w-4 h-4 text-purple-600 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                    </svg> Current Photo
                </label>
                <div class="relative rounded-lg overflow-hidden">
                    <img id="editCurrentImage" src="" alt="Current" class="w-full h-48 object-cover">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <svg class="w-4 h-4 text-red-500 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/>
                    </svg> Destination
                </label>
                <input type="text" name="destination" id="editDestination"
                       class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-purple-500 outline-none" 
                       placeholder="Destination" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <svg class="w-4 h-4 text-amber-600 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zm-5-7h-4v4h4v-4z"/>
                    </svg> Date
                </label>
                <input type="date" name="date" id="editDate"
                       class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-purple-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <svg class="w-4 h-4 text-yellow-500 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2l-2.81 6.63L2 9.24l5.46 4.73L5.82 21 12 17.27z"/>
                    </svg> Rating
                </label>
                <select name="rating" id="editRating"
                        class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-purple-500 outline-none">
                    @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ str_repeat('★', $i) }} ({{ $i }})</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <svg class="w-4 h-4 text-blue-600 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/>
                    </svg> Description
                </label>
                <textarea name="description" id="editDescription"
                          class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-purple-500 outline-none"
                          rows="3" placeholder="Description"></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <svg class="w-4 h-4 text-purple-600 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                    </svg> Change Photo (Optional)
                </label>
                <input type="file" name="image" accept="image/*" id="newImageInput"
                       class="w-full border-2 border-gray-200 rounded-lg p-2 focus:border-purple-500 outline-none"
                       onchange="previewNewImage(event)">
                <p class="text-xs text-gray-500 mt-1">Leave empty to keep current photo</p>
            </div>

            <div id="newImagePreview" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">🆕 New Photo Preview</label>
                <img id="newImagePreviewImg" src="" alt="New preview" class="w-full h-48 object-cover rounded-lg">
            </div>

            <button class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 
                           hover:to-blue-700 text-white py-3 rounded-lg font-semibold transition">
                💾 Save Changes
            </button>
        </form>
    </div>
</div>

<script>
function openEdit(trip) {
    console.log('openEdit called with trip:', trip);
    
    const modal = document.getElementById('editModal');
    if (!modal) {
        console.error('Edit modal not found!');
        return;
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const form = document.getElementById('editForm');
    if (!form) {
        console.error('Edit form not found!');
        return;
    }
    
    form.action = `/trip/${trip.id}`;
    
    const destinationInput = document.getElementById('editDestination');
    if (destinationInput) {
        destinationInput.value = trip.destination || '';
    }
    
    // Set date - already formatted as YYYY-MM-DD from PHP
    const dateInput = document.getElementById('editDate');
    if (dateInput) {
        dateInput.value = trip.date || '';
    }
    
    const ratingInput = document.getElementById('editRating');
    if (ratingInput) {
        ratingInput.value = trip.rating || 1;
    }
    
    const descriptionInput = document.getElementById('editDescription');
    if (descriptionInput) {
        descriptionInput.value = trip.description || '';
    }
    
    if (trip.image) {
        const currentImagePreview = document.getElementById('currentImagePreview');
        const currentImage = document.getElementById('editCurrentImage');
        if (currentImagePreview && currentImage) {
            // Build image URL - trip.image already contains the path like 'uploads/trips/...'
            const imageUrl = trip.image.startsWith('http') ? trip.image : '{{ url("/") }}/' + trip.image;
            currentImage.src = imageUrl;
            currentImagePreview.classList.remove('hidden');
        }
    } else {
        const currentImagePreview = document.getElementById('currentImagePreview');
        if (currentImagePreview) {
            currentImagePreview.classList.add('hidden');
        }
    }
    
    const newImagePreview = document.getElementById('newImagePreview');
    if (newImagePreview) {
        newImagePreview.classList.add('hidden');
    }
    
    const newImageInput = document.getElementById('newImageInput');
    if (newImageInput) {
        newImageInput.value = '';
    }
    
    // Set latitude and longitude (required by controller)
    const latitudeInput = document.getElementById('editLatitude');
    const longitudeInput = document.getElementById('editLongitude');
    if (latitudeInput) {
        latitudeInput.value = trip.latitude || '';
    }
    if (longitudeInput) {
        longitudeInput.value = trip.longitude || '';
    }
}

function closeEdit() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}

function previewNewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('newImagePreviewImg').src = e.target.result;
            document.getElementById('newImagePreview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('newImagePreview').classList.add('hidden');
    }
}

document.getElementById('editModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'editModal') closeEdit();
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeEdit();
});

// Print via a dedicated window; wait for all images to load so only one print dialog shows with pictures
function printPolaroids() {
    const template = document.getElementById('polaroidPrintTemplate');
    if (!template) return;
    const printWindow = window.open('', '_blank');
    if (!printWindow) {
        alert('Please allow pop-ups to print.');
        return;
    }
    const baseUrl = document.baseURI || window.location.origin + '/';
    var polaroidStyles =
        'body{margin:0;padding:0;background:#f5f5f5}' +
        '@page{margin:0.5in;size:A4}' +
        '.print-page{width:100%;padding:0.5in}' +
        '.polaroid-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20pt;justify-items:center}' +
        '.polaroid-card{break-inside:avoid;background:#fff;padding:12pt 12pt 28pt;width:100%;max-width:220px;' +
        'box-shadow:0 4px 14px rgba(0,0,0,0.18),0 10px 30px rgba(0,0,0,0.14);border:1px solid rgba(0,0,0,0.1)}' +
        '.polaroid-card .polaroid-image,.polaroid-card img.polaroid-image{width:100%;height:200px;object-fit:cover;display:block;' +
        'border:1px solid rgba(0,0,0,0.08);box-sizing:border-box}' +
        '.polaroid-card > div.polaroid-image{border:1px solid rgba(0,0,0,0.08);box-sizing:border-box}' +
        '.polaroid-caption{padding:10pt 4pt 0;font-family:\'Courier New\',monospace;font-size:11pt;color:#333;text-align:center}' +
        '.polaroid-location{font-weight:bold;margin-bottom:2pt}.polaroid-date{font-size:10pt;color:#666}';
    printWindow.document.write(
        '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Print Polaroids</title><style>' +
        polaroidStyles +
        '</style></head><body>' +
        template.innerHTML +
        '</body></html>'
    );
    var doc = printWindow.document;
    var imgs = doc.querySelectorAll('img[src]');
    // Resolve relative image src against site base
    imgs.forEach(function(img) {
        var src = img.getAttribute('src');
        if (src && !/^https?:|\/\//.test(src)) {
            img.src = new URL(src, baseUrl).href;
        }
    });
    printWindow.document.close();
    printWindow.focus();

    function doPrint() {
        printWindow.print();
        printWindow.onafterprint = function() { printWindow.close(); };
    }
    var pending = 0;
    imgs.forEach(function(img) {
        if (img.complete && img.naturalWidth) return;
        pending++;
        img.onload = img.onerror = function() {
            pending--;
            if (pending === 0) doPrint();
        };
    });
    if (pending === 0) doPrint();
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Print button
    const printBtn = document.getElementById('printPolaroidsBtn');
    if (printBtn) {
        printBtn.addEventListener('click', printPolaroids);
    }
    
    // Handle edit button clicks using event delegation (more reliable)
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-trip-btn');
        if (editBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            const trip = {
                id: editBtn.getAttribute('data-trip-id'),
                destination: editBtn.getAttribute('data-trip-destination') || '',
                date: editBtn.getAttribute('data-trip-date') || '',
                rating: parseInt(editBtn.getAttribute('data-trip-rating')) || 1,
                description: editBtn.getAttribute('data-trip-description') || '',
                image: editBtn.getAttribute('data-trip-image') || '',
                latitude: editBtn.getAttribute('data-trip-latitude') || '',
                longitude: editBtn.getAttribute('data-trip-longitude') || ''
            };
            
            console.log('Edit button clicked, trip data:', trip);
            openEdit(trip);
        }
    });
    
    // Debug: Log how many edit buttons were found
    const editButtons = document.querySelectorAll('.edit-trip-btn');
    console.log('Found', editButtons.length, 'edit buttons');
});
</script>
@endsection
