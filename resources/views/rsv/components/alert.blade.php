<div x-data="{ show: false, message: '', type: 'success' }"
     @notify.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 4000)"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     class="fixed top-5 right-5 z-50 px-4 py-3 rounded-lg shadow-xl text-white flex items-center space-x-3 text-sm font-medium"
     :class="type === 'success' ? 'bg-emerald-600' : 'bg-rose-600'"
     style="display: none;">
    <span x-text="message"></span>
    <button @click="show = false" class="text-white hover:text-gray-200 focus:outline-none">&times;</button>
</div>
