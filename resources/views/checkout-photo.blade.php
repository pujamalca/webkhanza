<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absen Pulang - {{ $absent->employee->name ?? 'Pegawai' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">📸 Absen Pulang</h1>
                        <p class="text-gray-600 mt-1">
                            {{ $absent->employee->name ?? 'Pegawai' }} - {{ $absent->date->format('d/m/Y') }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Masuk: {{ $absent->check_in }} | Status: {{ ucfirst($absent->status) }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-semibold text-green-600">
                            {{ now()->format('H:i:s') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ now()->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Camera Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4 text-center">Ambil Foto Absen Pulang</h2>
                
                <!-- Camera Status -->
                <div class="text-center mb-4">
                    <div id="camera_status" class="text-sm font-medium text-gray-700">📷 Memuat kamera...</div>
                </div>
                
                <!-- Camera Container -->
                <div class="bg-gray-100 rounded-lg overflow-hidden mx-auto mb-4" style="width: 370px; height: 300px;">
                    <div id="my_camera" class="w-full h-full flex items-center justify-center">
                        <div class="text-gray-500">Memuat kamera...</div>
                    </div>
                </div>
                
                <!-- Photo Preview -->
                <div id="results" class="text-center mb-4 min-h-[50px]"></div>
                
                <!-- Camera Controls -->
                <div class="flex justify-center gap-4 mb-6">
                    <button id="btn_capture" type="button" 
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                        📸 Ambil Foto Pulang
                    </button>
                    
                    <button id="btn_retake" type="button"
                            class="px-4 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors font-medium"
                            style="display: none;">
                        🔄 Foto Ulang
                    </button>
                </div>

                <!-- Form Submit -->
                <form method="POST" action="{{ route('checkout-photo.store', $absent->id) }}" id="checkout_form">
                    @csrf
                    
                    <input type="hidden" name="photo_data" id="photo_data_field">
                    
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Pulang (Opsional)
                        </label>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                  placeholder="Contoh: Lembur sampai malam, menyelesaikan laporan bulanan...">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="flex justify-between">
                        <a href="/admin/pegawai/absents" 
                           class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            ← Kembali
                        </a>
                        
                        <button type="submit" id="submit_btn"
                                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            ✅ Konfirmasi Absen Pulang
                        </button>
                    </div>
                </form>
            </div>

            <!-- Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>

    <script>
        console.log('🚀 CHECKOUT PHOTO PAGE LOADED');
        
        // Configure webcam
        Webcam.set({
            width: 370,
            height: 300,
            image_format: 'jpeg',
            jpeg_quality: 90,
            constraints: {
                video: true,
                facingMode: "user"
            }
        });
        
        // Initialize camera
        setTimeout(function() {
            console.log('🎬 Initializing checkout camera...');
            try {
                Webcam.attach('#my_camera');
                document.getElementById('camera_status').innerHTML = '✅ Kamera aktif - Siap mengambil foto pulang';
                console.log('✅ Checkout camera attached successfully');
            } catch(e) {
                console.error('❌ Camera error:', e);
                document.getElementById('camera_status').innerHTML = '❌ Gagal mengakses kamera: ' + e.message;
            }
        }, 1000);
        
        // Take snapshot function
        document.getElementById('btn_capture').addEventListener('click', function() {
            console.log('📸 Taking checkout snapshot...');
            
            Webcam.snap(function(data_uri) {
                console.log('📷 Checkout photo captured, length:', data_uri.length);
                
                // Show preview
                document.getElementById('results').innerHTML = 
                    '<img src="' + data_uri + '" class="max-w-sm mx-auto rounded-lg shadow-md"/>';
                
                // Save to form field
                document.getElementById('photo_data_field').value = data_uri;
                
                // Save to localStorage as backup
                localStorage.setItem('checkout_photo_backup', data_uri);
                
                // Send to session via AJAX
                fetch('/store-photo-temp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        photo_data: data_uri,
                        type: 'check_out'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('✅ AJAX session storage response:', data);
                })
                .catch(error => {
                    console.error('❌ AJAX session storage error:', error);
                });
                
                // Update UI
                document.getElementById('camera_status').innerHTML = '📸 Foto pulang berhasil diambil!';
                document.getElementById('btn_capture').style.display = 'none';
                document.getElementById('btn_retake').style.display = 'inline-block';
                // Photo captured - submit button already enabled
                
                console.log('✅ Checkout photo ready for submit');
            });
        });
        
        // Retake photo function
        document.getElementById('btn_retake').addEventListener('click', function() {
            console.log('🔄 Retaking checkout photo...');
            
            document.getElementById('results').innerHTML = '';
            document.getElementById('camera_status').innerHTML = '✅ Kamera aktif - Siap mengambil foto pulang';
            document.getElementById('btn_capture').style.display = 'inline-block';
            document.getElementById('btn_retake').style.display = 'none';
            // Photo cleared - submit button remains enabled
            
            // Clear form field
            document.getElementById('photo_data_field').value = '';
            
            // Clear localStorage backup
            localStorage.removeItem('checkout_photo_backup');
            
            console.log('✅ Ready for new checkout photo');
        });
        
        // Form submission handler
        document.getElementById('checkout_form').addEventListener('submit', function(e) {
            const photoField = document.getElementById('photo_data_field');
            
            // Photo is optional - no validation required
            // Final backup: inject backup photo if available
            const backupPhoto = localStorage.getItem('checkout_photo_backup');
            if (!photoField.value && backupPhoto) {
                photoField.value = backupPhoto;
                console.log('✅ Injected backup photo to form field');
            }
            
            console.log('✅ Form submitting, photo data length:', photoField.value ? photoField.value.length : 0);
            return true;
        });
        
        console.log('✅ Checkout photo page ready');
    </script>
</body>
</html>