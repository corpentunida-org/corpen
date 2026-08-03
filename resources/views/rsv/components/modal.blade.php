@props(['name', 'title' => ''])
<div x-data="{ open: false, title: '{{ $title }}' }"
     @open-modal-{{ $name }}.window="open = true; if($event.detail && $event.detail.title) title = $event.detail.title;"
     @close-modal-{{ $name }}.window="open = false"
     x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-60 flex items-center justify-center p-4 backdrop-blur-sm"
     style="display: none;">
    <div @click.away="open = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="bg-white rounded-xl max-w-2xl w-full p-6 shadow-2xl relative border border-gray-100">
        <div class="flex justify-between items-center pb-4 border-b border-gray-100 mb-4">
            <h3 class="text-xl font-bold text-gray-800" x-text="title"></h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>
        <div class="max-h-[75vh] overflow-y-auto pr-1">
            {{ $slot }}
        </div>
    </div>
</div>
