<div x-data="cameraCapture('{{ $field_name }}', '{{ $label }}')" class="space-y-4">
    <div class="text-sm font-medium text-gray-700">{{ $label }}</div>
    
    <!-- Camera Preview -->
    <div class="relative bg-gray-100 rounded-lg overflow-hidden" x-show="!useFileUpload">
        <video 
            x-ref="video" 
            x-show="!captured && cameraActive"
            class="w-full h-64 object-cover"
            autoplay
            playsinline
        ></video>
        
        <!-- Captured Photo Preview -->
        <div x-show="captured" class="w-full h-64 flex items-center justify-center">
            <img 
                x-ref="preview" 
                class="max-w-full max-h-full object-cover rounded"
                style="display: none;"
            />
        </div>
        
        <!-- Loading State -->
        <div x-show="!cameraActive && !captured && !cameraError" class="w-full h-64 flex items-center justify-center">
            <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto"></div>
                <p class="mt-2 text-sm text-gray-500">Memuat kamera...</p>
            </div>
        </div>
        
        <!-- Camera Error State -->
        <div x-show="cameraError" class="w-full h-64 flex items-center justify-center">
            <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <p class="text-sm text-gray-500">Kamera tidak dapat diakses</p>
                <button 
                    type="button"
                    @click="switchToFileUpload()"
                    class="mt-2 text-sm text-primary-600 hover:text-primary-500"
                >
                    Gunakan upload file sebagai gantinya
                </button>
            </div>
        </div>
    </div>
    
    <!-- File Upload Fallback -->
    <div x-show="useFileUpload" class="space-y-2">
        <input 
            type="file" 
            x-ref="fileInput"
            @change="handleFileUpload($event)"
            accept="image/*,capture=camera"
            capture="user"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
        />
        <p class="text-xs text-gray-500">Pilih foto atau ambil dengan kamera perangkat</p>
        
        <!-- File Preview -->
        <div x-show="fileUploaded" class="w-full h-64 flex items-center justify-center bg-gray-100 rounded-lg">
            <img 
                x-ref="filePreview" 
                class="max-w-full max-h-full object-cover rounded"
                style="display: none;"
            />
        </div>
    </div>
    
    <!-- Controls -->
    <div class="flex justify-center space-x-3" x-show="!useFileUpload">
        <!-- Start Camera Button -->
        <button 
            type="button"
            x-show="!cameraActive && !captured && !cameraError"
            @click="startCamera()"
            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Aktifkan Kamera
        </button>
        
        <!-- Capture Button -->
        <button 
            type="button"
            x-show="cameraActive && !captured"
            @click="capturePhoto()"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Ambil Foto
        </button>
        
        <!-- Retake Button -->
        <button 
            type="button"
            x-show="captured"
            @click="retakePhoto()"
            class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Ambil Ulang
        </button>
    </div>
    
    <!-- File Upload Controls -->
    <div class="flex justify-center space-x-3" x-show="useFileUpload">
        <button 
            type="button"
            x-show="fileUploaded"
            @click="clearFileUpload()"
            class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Pilih Ulang
        </button>
        
        <button 
            type="button"
            @click="switchToCamera()"
            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Coba Kamera Lagi
        </button>
    </div>
    
    <!-- Hidden canvas for photo capture -->
    <canvas x-ref="canvas" style="display: none;"></canvas>
</div>

<script>
function cameraCapture(fieldName, label) {
    return {
        cameraActive: false,
        captured: false,
        stream: null,
        useFileUpload: false,
        cameraError: false,
        fileUploaded: false,
        
        async startCamera() {
            this.cameraError = false;
            
            try {
                // Check if getUserMedia is available
                const getUserMedia = navigator.mediaDevices?.getUserMedia || 
                                   navigator.getUserMedia || 
                                   navigator.webkitGetUserMedia || 
                                   navigator.mozGetUserMedia || 
                                   navigator.msGetUserMedia;

                if (!getUserMedia) {
                    throw new Error('getUserMedia is not supported');
                }

                // Use the available method
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    // Modern browsers
                    this.stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { 
                            facingMode: 'user',
                            width: { ideal: 800 },
                            height: { ideal: 600 }
                        } 
                    });
                } else {
                    // Fallback for older browsers
                    const constraints = { 
                        video: { 
                            facingMode: 'user',
                            width: { ideal: 800 },
                            height: { ideal: 600 }
                        } 
                    };
                    
                    this.stream = await new Promise((resolve, reject) => {
                        const gum = navigator.getUserMedia || 
                                   navigator.webkitGetUserMedia || 
                                   navigator.mozGetUserMedia || 
                                   navigator.msGetUserMedia;
                        
                        gum.call(navigator, constraints, resolve, reject);
                    });
                }
                
                this.$refs.video.srcObject = this.stream;
                this.cameraActive = true;
                
            } catch (error) {
                console.error('Error accessing camera:', error);
                this.cameraError = true;
                this.cameraActive = false;
            }
        },
        
        capturePhoto() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            const preview = this.$refs.preview;
            const context = canvas.getContext('2d');
            
            // Set canvas size
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            // Draw video frame to canvas
            context.drawImage(video, 0, 0);
            
            // Convert to blob and create preview
            canvas.toBlob((blob) => {
                if (blob) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        
                        // Set the hidden input value
                        const hiddenInput = document.querySelector(`input[name="${fieldName}"]`);
                        if (hiddenInput) {
                            hiddenInput.value = e.target.result;
                            
                            // Trigger change event for Livewire
                            hiddenInput.dispatchEvent(new Event('input'));
                        }
                    };
                    reader.readAsDataURL(blob);
                }
            }, 'image/jpeg', 0.8);
            
            this.captured = true;
            this.stopCamera();
        },
        
        retakePhoto() {
            this.captured = false;
            this.$refs.preview.style.display = 'none';
            
            // Clear the hidden input
            const hiddenInput = document.querySelector(`input[name="${fieldName}"]`);
            if (hiddenInput) {
                hiddenInput.value = '';
                hiddenInput.dispatchEvent(new Event('input'));
            }
            
            this.startCamera();
        },
        
        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.cameraActive = false;
        },
        
        switchToFileUpload() {
            this.useFileUpload = true;
            this.cameraError = false;
            this.stopCamera();
        },
        
        switchToCamera() {
            this.useFileUpload = false;
            this.fileUploaded = false;
            this.clearFileInput();
            this.startCamera();
        },
        
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.$refs.filePreview.src = e.target.result;
                    this.$refs.filePreview.style.display = 'block';
                    
                    // Set the hidden input value
                    const hiddenInput = document.querySelector(`input[name="${fieldName}"]`);
                    if (hiddenInput) {
                        hiddenInput.value = e.target.result;
                        hiddenInput.dispatchEvent(new Event('input'));
                    }
                    
                    this.fileUploaded = true;
                };
                reader.readAsDataURL(file);
            }
        },
        
        clearFileUpload() {
            this.fileUploaded = false;
            this.clearFileInput();
            this.$refs.filePreview.style.display = 'none';
            
            // Clear the hidden input
            const hiddenInput = document.querySelector(`input[name="${fieldName}"]`);
            if (hiddenInput) {
                hiddenInput.value = '';
                hiddenInput.dispatchEvent(new Event('input'));
            }
        },
        
        clearFileInput() {
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },

        init() {
            // Auto start camera when component loads
            this.$nextTick(() => {
                if (!this.useFileUpload) {
                    this.startCamera();
                }
            });
        }
    }
}
</script>