<!-- Camera view now relies on global camera script loaded via AppServiceProvider -->
<script>
// Initialize camera with the correct type when this view loads
console.log('📷 Camera view loaded with type: {{ $type ?? "check_in" }}');

// Set the photo type and initialize
if (typeof window.initWebcam === 'function') {
    console.log('✅ Global camera script available, initializing...');
    window.initWebcam('{{ $type ?? "check_in" }}');
} else {
    console.log('⚠️  Global camera script not yet loaded, waiting...');
    // Retry initialization when global script is ready
    const checkScript = setInterval(() => {
        if (typeof window.initWebcam === 'function') {
            console.log('✅ Global camera script now available, initializing...');
            window.initWebcam('{{ $type ?? "check_in" }}');
            clearInterval(checkScript);
        }
    }, 100);
    
    // Timeout after 10 seconds
    setTimeout(() => {
        clearInterval(checkScript);
        console.error('❌ Global camera script failed to load within 10 seconds');
    }, 10000);
}
</script>

<div class="space-y-4">
    <!-- Status -->
    <div class="text-center">
        <div id="camera_status" class="text-sm font-medium">📷 Memuat kamera...</div>
    </div>
    
    <!-- Camera Preview -->
    <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
        <div id="my_camera" class="w-full h-full flex items-center justify-center">
            <div class="text-gray-500">Memuat kamera...</div>
        </div>
    </div>
    
    <!-- Photo Preview -->
    <div id="results" class="text-center"></div>
    
    <!-- Controls -->
    <div class="flex justify-center space-x-3">
        <button id="btn_capture" 
                onclick="take_snapshot()"
                class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors">
            📸 Ambil Foto {{ ($type ?? 'check_in') === 'check_out' ? 'Pulang' : 'Masuk' }}
        </button>
        
        <button id="btn_retake" 
                onclick="retake_photo()"
                class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 transition-colors"
                style="display: none;">
            🔄 Foto Ulang
        </button>
        
        <!-- Debug button -->
        <button type="button" 
                onclick="debugFields()"
                class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600 transition-colors">
            🔍 Debug Fields
        </button>
    </div>
    
    <!-- Debug: Add multiple fallback inputs -->
    <input type="hidden" name="check_in_photo" id="check_in_photo_debug" value="" />
    <textarea name="check_in_photo_backup" id="check_in_photo_backup" style="display: none;"></textarea>
</div>