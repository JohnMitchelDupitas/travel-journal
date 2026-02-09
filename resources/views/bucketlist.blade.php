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
<div class="min-h-screen bg-gray-50 p-6">

    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex justify-between items-center">
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
    </div>


    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            @forelse($buckets as $bucket)
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-all border border-gray-100">
                    
           
                    <div class="mb-3">
                        <h3 class="text-xl font-bold text-gray-900">
                            {{ $bucket->destination }}
                        </h3>
                    </div>
               
                    <p class="text-sm text-gray-600 mb-4 min-h-[40px]">
                        {{ $bucket->description ?? 'No description provided' }}
                    </p>

                    <div class="flex justify-between items-center pt-4 border-t border-gray-100">
         
                        <span class="text-xs font-bold px-3 py-1.5 rounded-full
                            {{ $bucket->priority == 'high' ? 'bg-gradient-to-r from-red-500 to-pink-500 text-white shadow-sm' :
                               ($bucket->priority == 'medium' ? 'bg-gradient-to-r from-yellow-400 to-orange-400 text-white shadow-sm' :
                               'bg-gradient-to-r from-green-500 to-emerald-500 text-white shadow-sm') }}">
                            {{ ucfirst($bucket->priority) }}
                        </span>

             
                        <div class="flex gap-3">
                          
                            <button 
                                onclick='openEditModal(@json($bucket))'
                                class="text-blue-600 hover:text-blue-700 font-semibold text-sm transition-colors">
                                Edit
                            </button>

                           
                            <form 
                                action="{{ route('bucket.destroy', $bucket->id) }}" 
                                method="POST" 
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit"
                                    onclick="return confirm('Are you sure you want to delete {{ $bucket->destination }}?')"
                                    class="text-red-600 hover:text-red-700 font-semibold text-sm transition-colors">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
          
                <div class="col-span-full bg-white rounded-2xl border border-gray-100 py-16 text-center shadow-sm">
                    <div class="mb-4 flex justify-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center text-4xl">
                            ✨
                        </div>
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
                <input 
                    type="text" 
                    name="destination" 
                    id="destination"
                    placeholder="e.g., Tokyo, Japan"
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none" 
                    required>
            </div>
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
                    <option value="low">🟢 Low - Someday maybe</option>
                    <option value="medium" selected>🟡 Medium - Next year or two</option>
                    <option value="high">🔴 High - Must visit soon!</option>
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
</script>
@endsection