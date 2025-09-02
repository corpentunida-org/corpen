<x-base-layout>
    <div class="container mx-auto my-6 p-4 bg-white rounded-xl shadow-md">
        <h2 class="text-xl font-bold mb-4">Control de llamadas</h2>

        <!-- Iframe con la página de Google Apps Script -->
        <iframe 
            src="https://script.google.com/macros/s/AKfycbyoNT3TI22RfgBSrTvnLHkjVoLuB9maQl68ScCOMJ9jztMCkQ09B6i6Q3wRaxWqStGp/exec"
            width="100%" 
            height="700px" 
            style="border:1px solid #ccc; border-radius:12px;"
            loading="lazy"
            sandbox="allow-scripts allow-forms allow-same-origin allow-popups">
        </iframe>
    </div>
</x-base-layout>
