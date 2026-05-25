   <!-- Preloader Overlay -->
    <div id="refreshPreloader" class="fixed inset-0 bg-white  flex items-center justify-center z-[9999] hidden">
        <div class="text-center">
            <!-- Spinner -->
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <!-- Loading Text -->
            <p class="text-gray-600 text-sm font-medium">Loading...</p>
        </div>
    </div>



 <script>
    const preloader = document.getElementById('refreshPreloader');

    preloader.classList.add('hidden');

    window.addEventListener('beforeunload', () => {
        preloader.classList.remove('hidden');
    });

    window.addEventListener('load', () => {
        preloader.classList.add('hidden');
    });

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            preloader.classList.add('hidden');
        }
    });

    window.addEventListener('popstate', () => {
        preloader.classList.add('hidden');
    });
</script>
