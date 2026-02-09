
@extends('layout')

@section('content')
<div class="flex-1 overflow-y-auto bg-gradient-to-br from-purple-50 to-blue-50">
    <div class="p-6 md:p-10 max-w-7xl mx-auto">

        <div class="mb-10 text-center">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-2">
                Memory Gallery
            </h1>
            <p class="text-gray-600">{{ $trips->count() }} beautiful moments</p>
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
                    <p class="text-yellow-400 text-sm mb-3">{{ str_repeat('★', $trip->rating) }}</p>

                    <div class="flex gap-2">
                        <button onclick="openEdit({{ $trip }})"
                                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-2 rounded-lg 
                                       transition transform hover:scale-105">
                            Edit
                        </button>
                        <form action="{{ route('trip.destroy', $trip->id) }}" method="POST"
                              onsubmit="return confirm('Delete this memory?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-2 rounded-lg
                                           transition transform hover:scale-105">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="flex flex-col items-center justify-center py-24 bg-white/80 rounded-2xl border-2 border-dashed border-purple-200">
            <div class="text-7xl mb-4">📷</div>
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
<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-6 text-white rounded-t-2xl relative">
            <button onclick="closeEdit()" class="absolute top-4 right-4 text-white text-2xl hover:bg-white/20 
                                                  rounded-full w-8 h-8 flex items-center justify-center">✕</button>
            <h2 class="text-2xl font-bold">Edit Memory</h2>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div id="currentImagePreview" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-2">📷 Current Photo</label>
                <div class="relative rounded-lg overflow-hidden">
                    <img id="editCurrentImage" src="" alt="Current" class="w-full h-48 object-cover">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">📍 Destination</label>
                <input type="text" name="destination" id="editDestination"
                       class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-purple-500 outline-none" 
                       placeholder="Destination" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">📅 Date</label>
                <input type="date" name="date" id="editDate"
                       class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-purple-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">⭐ Rating</label>
                <select name="rating" id="editRating"
                        class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-purple-500 outline-none">
                    @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ str_repeat('⭐', $i) }} ({{ $i }})</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">📝 Description</label>
                <textarea name="description" id="editDescription"
                          class="w-full border-2 border-gray-200 rounded-lg p-3 focus:border-purple-500 outline-none"
                          rows="3" placeholder="Description"></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">🖼️ Change Photo (Optional)</label>
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
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.getElementById('editForm').action = `/trip/${trip.id}`;
    
    document.getElementById('editDestination').value = trip.destination;
    document.getElementById('editDate').value = trip.date;
    document.getElementById('editRating').value = trip.rating;
    document.getElementById('editDescription').value = trip.description ?? '';
    
    if (trip.image) {
        const currentImagePreview = document.getElementById('currentImagePreview');
        const currentImage = document.getElementById('editCurrentImage');
        currentImage.src = '{{ asset('') }}' + trip.image;
        currentImagePreview.classList.remove('hidden');
    }
    
    document.getElementById('newImagePreview').classList.add('hidden');
    document.getElementById('newImageInput').value = '';
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
</script>
@endsection
