@extends('layout')

@section('content')
<div class="flex-1 overflow-y-auto">
    <div class="p-6 md:p-10 max-w-7xl mx-auto animate-fade">
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Bucket List</h1>
            <p class="text-gray-600">Places you dream of visiting</p>
        </div>

        <!-- Add Button -->
        <button onclick="openBucketModal()" class="mb-8 inline-flex items-center gap-2 px-6 py-3 gradient-primary text-white rounded-lg font-semibold hover:shadow-lg transition-all duration-300 transform hover:scale-105">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Destination
        </button>

        <!-- Bucket List Grid -->
        <div id="bucketGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($buckets as $bucket)
                <div class="group bg-white rounded-2xl border-2 p-8 card-shadow-hover transition-all duration-300" style="border-color: {{ $bucket->priority === 'high' ? '#ef4444' : ($bucket->priority === 'medium' ? '#f59e0b' : '#10b981') }}">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1 pr-4">
                            <h3 class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition">{{ $bucket->destination }}</h3>
                        </div>
                        
                        <!-- Delete Button -->
                        <form action="{{ route('bucket.destroy', $bucket->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 transition p-2 hover:bg-red-50 rounded-lg" onclick="return confirm('Remove from bucket list?')">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Description -->
                    @if($bucket->description)
                        <p class="text-gray-600 text-sm mb-6 leading-relaxed">{{ $bucket->description }}</p>
                    @else
                        <p class="text-gray-400 text-sm mb-6 italic">No description added</p>
                    @endif
                    
                    <!-- Footer -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-100">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $bucket->priority === 'high' ? 'bg-red-100 text-red-700' : ($bucket->priority === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                            {{ ucfirst($bucket->priority) }}
                        </span>
                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($bucket->created_at)->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border-2 border-dashed border-gray-300">
                        <div class="text-6xl mb-4">✨</div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No bucket list items yet</h3>
                        <p class="text-gray-600 mb-6 text-center max-w-md">Add your dream destinations and start planning your next adventure</p>
                        <button onclick="openBucketModal()" class="px-6 py-3 gradient-primary text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                            + Add First Destination
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Bucket Item Modal -->
<div id="bucketModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fade">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full animate-slide">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Add to Bucket List</h2>
            <p class="text-gray-600 text-sm mt-1">Dream big, plan bigger</p>
        </div>
        
        <form action="{{ route('bucket.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <!-- Destination Input -->
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Destination *</label>
                <input type="text" name="destination" placeholder="e.g. Tokyo, Japan" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition" required>
                @error('destination') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Description Input -->
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Why do you want to go?</label>
                <textarea name="description" placeholder="Share your dream..." rows="3" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition resize-none"></textarea>
                @error('description') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Priority Dropdown -->
            <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Priority *</label>
                <select name="priority" class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 transition" required>
                    <option value="low">🟢 Low - Someday maybe</option>
                    <option value="medium" selected>🟡 Medium - Would like to go</option>
                    <option value="high">🔴 High - Must do soon!</option>
                </select>
                @error('priority') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-6">
                <button type="button" onclick="closeBucketModal()" class="flex-1 px-4 py-3 text-gray-700 border-2 border-gray-200 rounded-lg hover:bg-gray-50 transition font-semibold">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 gradient-primary text-white rounded-lg hover:shadow-lg transition font-semibold transform hover:scale-105 duration-200">
                    Add to List
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openBucketModal() {
    document.getElementById('bucketModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeBucketModal() {
    document.getElementById('bucketModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('bucketModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'bucketModal') closeBucketModal();
});

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeBucketModal();
});
</script>
@endsection

