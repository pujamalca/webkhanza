<div x-data="{
    status: 'inactive',
    captured: false,
    stream: null,
    
    async startCamera() {
        this.status = 'loading';
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            this.$refs.video.srcObject = this.stream;
            this.status = 'active';
        } catch (error) {
            this.status = 'error';
        }
    },
    
    capture() {
        const video = this.$refs.video;
        const canvas = this.$refs.canvas;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        canvas.toBlob((blob) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.$refs.preview.src = e.target.result;
                document.querySelector('[name=photo_data]').value = e.target.result;
                this.captured = true;
                this.stopCamera();
            };
            reader.readAsDataURL(blob);
        });
    },
    
    retake() {
        this.captured = false;
        this.startCamera();
    },
    
    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.status = 'inactive';
        }
    }
}" 
x-init="startCamera()" 
class="space-y-4">

    <!-- Status -->
    <div class="text-center">
        <span x-show="status === 'loading'">📷 Mengaktifkan kamera...</span>
        <span x-show="status === 'active'">✅ Kamera aktif</span>
        <span x-show="status === 'error'">❌ Kamera error</span>
        <span x-show="captured">📸 Foto berhasil diambil</span>
    </div>
    
    <!-- Camera Preview -->
    <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
        
        <!-- Video -->
        <video x-ref="video" 
               x-show="status === 'active' && !captured"
               class="w-full h-full object-cover" 
               autoplay playsinline muted>
        </video>
        
        <!-- Photo Preview -->
        <img x-ref="preview" 
             x-show="captured"
             class="w-full h-full object-cover">
             
        <!-- Loading -->
        <div x-show="status === 'loading'" 
             class="absolute inset-0 flex items-center justify-center">
            <div class="animate-spin w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full"></div>
        </div>
        
        <!-- Error -->
        <div x-show="status === 'error'" 
             class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
                <p class="text-red-600 mb-2">Kamera tidak dapat diakses</p>
                <button @click="startCamera()" 
                        class="px-3 py-1 bg-blue-600 text-white rounded">
                    Coba Lagi
                </button>
            </div>
        </div>
    </div>
    
    <!-- Controls -->
    <div class="flex justify-center space-x-3">
        
        <!-- Start Button -->
        <button x-show="status === 'inactive'" 
                @click="startCamera()"
                class="px-4 py-2 bg-blue-600 text-white rounded">
            Aktifkan Kamera
        </button>
        
        <!-- Capture Button -->
        <button x-show="status === 'active' && !captured" 
                @click="capture()"
                class="px-6 py-2 bg-green-600 text-white rounded">
            Ambil Foto
        </button>
        
        <!-- Retake Button -->
        <button x-show="captured" 
                @click="retake()"
                class="px-4 py-2 bg-amber-600 text-white rounded">
            Foto Ulang
        </button>
    </div>
    
    <canvas x-ref="canvas" style="display: none;"></canvas>
</div>