<!-- Load Webcam.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<script>
// Configure Webcam.js
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

function initWebcam() {
    console.log('=== INITIALIZING WEBCAM.JS ===');
    console.log('Available fields:', Array.from(document.querySelectorAll('input, textarea, select')).map(f => ({name: f.name, type: f.type, id: f.id})));
    
    // Attach webcam after DOM is ready
    setTimeout(() => {
        console.log('Attaching webcam to #my_camera...');
        Webcam.attach('#my_camera');
        document.getElementById('camera_status').innerHTML = '✅ Kamera aktif - Siap mengambil foto';
    }, 500);
}

// Global flag to prevent ANY form submission
window.PHOTO_CAPTURE_IN_PROGRESS = false;

function take_snapshot() {
    console.log('=== TAKING SNAPSHOT ===');
    console.log('🚫 ACTIVATING GLOBAL SUBMIT PREVENTION...');
    
    // SET GLOBAL FLAG
    window.PHOTO_CAPTURE_IN_PROGRESS = true;
    
    // NUCLEAR OPTION: Completely disable ALL form submissions
    const originalFormSubmit = HTMLFormElement.prototype.submit;
    const originalDispatchEvent = Element.prototype.dispatchEvent;
    
    HTMLFormElement.prototype.submit = function() {
        console.log('🚫 BLOCKED HTMLFormElement.submit() during photo capture!');
        return false;
    };
    
    Element.prototype.dispatchEvent = function(event) {
        if (window.PHOTO_CAPTURE_IN_PROGRESS && event.type === 'submit') {
            console.log('🚫 BLOCKED dispatchEvent(submit) during photo capture!');
            event.preventDefault();
            event.stopImmediatePropagation();
            return false;
        }
        return originalDispatchEvent.call(this, event);
    };
    
    // Block ALL event listeners on ALL forms
    const allForms = document.querySelectorAll('form');
    const blockedEvents = [];
    
    allForms.forEach((form, formIndex) => {
        // Clone form to remove ALL event listeners
        const newForm = form.cloneNode(true);
        
        // Add our own submit blocker
        newForm.addEventListener('submit', function(e) {
            if (window.PHOTO_CAPTURE_IN_PROGRESS) {
                console.log('🚫 BLOCKED cloned form submit during photo capture!');
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });
        
        // Replace original form with cleaned form
        form.parentNode.replaceChild(newForm, form);
        blockedEvents.push({ original: form, replacement: newForm });
    });
    
    // Block ALL possible submit events at document level
    const submitBlocker = function(e) {
        if (window.PHOTO_CAPTURE_IN_PROGRESS) {
            console.log('🚫 BLOCKED document-level submit during photo capture!');
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
    };
    
    document.addEventListener('submit', submitBlocker, true);
    document.addEventListener('submit', submitBlocker, false);
    
    Webcam.snap(function(data_uri) {
        console.log('Snapshot captured, data length:', data_uri.length);
        console.log('Data preview:', data_uri.substring(0, 50));
        
        // Show preview
        document.getElementById('results').innerHTML = '<img src="' + data_uri + '" style="max-width: 300px; border-radius: 8px;"/>';
        
        // Save to localStorage IMMEDIATELY (most reliable)
        try {
            localStorage.setItem('absen_photo_backup', data_uri);
            console.log('Photo saved to localStorage as primary backup');
        } catch(e) {
            console.error('Failed to save to localStorage:', e);
        }
        
        // Save to multiple fields WITHOUT triggering events that cause submission
        const fields = ['check_in_photo', 'check_in_photo_backup', 'photo_data', 'check_out_photo'];
        let fieldFound = false;
        
        for (let i = 0; i < fields.length; i++) {
            const field = document.querySelector('[name=' + fields[i] + ']');
            if (field) {
                field.value = data_uri;
                console.log('Photo saved to field:', fields[i], 'Length:', field.value.length);
                fieldFound = true;
            }
        }
        
        // Also try by ID
        const idFields = ['check_in_photo_debug', 'check_in_photo_backup'];
        for (let i = 0; i < idFields.length; i++) {
            const field = document.getElementById(idFields[i]);
            if (field) {
                field.value = data_uri;
                console.log('Photo saved to field by ID:', idFields[i], 'Length:', field.value.length);
                fieldFound = true;
            }
        }
        
        if (!fieldFound) {
            console.log('Creating emergency hidden field...');
            const emergencyField = document.createElement('input');
            emergencyField.type = 'hidden';
            emergencyField.name = 'check_in_photo_emergency';
            emergencyField.value = data_uri;
            document.body.appendChild(emergencyField);
            console.log('Emergency field created with length:', data_uri.length);
        }
        
        // Update UI
        document.getElementById('camera_status').innerHTML = '📸 Foto berhasil diambil - JANGAN SUBMIT DULU!';
        document.getElementById('btn_capture').style.display = 'none';
        document.getElementById('btn_retake').style.display = 'inline-block';
        
        // BACKUP METHOD: Send photo to session via AJAX (delayed to avoid prevention)
        setTimeout(() => {
            console.log('📤 Sending photo to session via AJAX...');
            fetch('/store-photo-temp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    photo_data: data_uri
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('📤 AJAX response:', data);
                if (data.success) {
                    console.log('✅ Photo stored in session successfully, length:', data.length);
                }
            })
            .catch(error => {
                console.error('❌ AJAX error:', error);
            });
        }, 2500); // Send after form restoration is complete
        
        // THIRD BACKUP: Try Livewire method call (disabled for now)
        // setTimeout(() => {
        //     console.log('📤 Trying Livewire method call...');
        //     try {
        //         if (typeof Livewire !== 'undefined' && Livewire.find) {
        //             const component = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
        //             if (component) {
        //                 component.call('storePhotoData', data_uri);
        //                 console.log('✅ Livewire method call sent');
        //             } else {
        //                 console.log('❌ Livewire component not found');
        //             }
        //         } else {
        //             console.log('❌ Livewire not available');
        //         }
        //     } catch (e) {
        //         console.error('❌ Livewire call error:', e);
        //     }
        // }, 3000); // Send after AJAX attempt
        
        // FOURTH BACKUP: Split into chunks for large data
        if (data_uri.length > 10000) {
            console.log('📤 Splitting large photo into chunks...');
            const chunkSize = 8000;
            const totalChunks = Math.ceil(data_uri.length / chunkSize);
            
            for (let i = 0; i < Math.min(totalChunks, 5); i++) {
                const start = i * chunkSize;
                const end = Math.min(start + chunkSize, data_uri.length);
                const chunk = data_uri.substring(start, end);
                const fieldName = `photo_chunk_${i + 1}`;
                
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.value = chunk;
                    console.log(`✅ Chunk ${i + 1} saved: ${chunk.length} chars`);
                }
            }
            
            // Store chunk count
            const countField = document.querySelector('[name="photo_chunk_count"]');
            if (countField) {
                countField.value = Math.min(totalChunks, 5);
                console.log(`✅ Chunk count saved: ${Math.min(totalChunks, 5)}`);
            }
        }
        
        // Restore ALL form functionality after delay
        setTimeout(() => {
            console.log('🔄 RESTORING FORM SUBMISSION CAPABILITY...');
            
            // Restore global flag
            window.PHOTO_CAPTURE_IN_PROGRESS = false;
            
            // Restore original prototypes
            HTMLFormElement.prototype.submit = originalFormSubmit;
            Element.prototype.dispatchEvent = originalDispatchEvent;
            
            // Remove document-level blockers
            document.removeEventListener('submit', submitBlocker, true);
            document.removeEventListener('submit', submitBlocker, false);
            
            // Restore original forms by refreshing page content or replacing back
            // This is the safest approach to restore Filament functionality
            
            document.getElementById('camera_status').innerHTML = '📸 Foto berhasil diambil - SIAP SUBMIT SEKARANG!';
            
            console.log('✅ FORM SUBMISSION RESTORED - Ready to submit!');
            
            // Add form submission interceptor to debug what gets sent
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    console.log('=== FORM SUBMISSION INTERCEPTED ===');
                    const formData = new FormData(this);
                    for (let [key, value] of formData.entries()) {
                        if (key.includes('check_in_photo')) {
                            console.log(`Form data ${key}: ${typeof value === 'string' ? value.length + ' chars' : value}`);
                        }
                    }
                }, true);
            });
            
        }, 2000);  // Increased delay to ensure photo processing is complete
        
        // Verification: Check all fields after save
        setTimeout(() => {
            console.log('=== FIELD VERIFICATION AFTER PHOTO ===');
            
            fields.concat(['check_in_photo_emergency']).forEach(fieldName => {
                const field = document.querySelector('[name=' + fieldName + ']');
                if (field && field.value) {
                    console.log(`✅ Field ${fieldName} has photo data: ${field.value.length} chars`);
                }
            });
            
            const backupCheck = localStorage.getItem('absen_photo_backup');
            console.log('✅ localStorage backup:', backupCheck ? `${backupCheck.length} chars` : 'MISSING');
            
        }, 200);
    });
}

function retake_photo() {
    console.log('=== RETAKING PHOTO ===');
    console.log('🚫 ACTIVATING RETAKE SUBMIT PREVENTION...');
    
    // SET GLOBAL FLAG TO PREVENT ANY SUBMIT DURING RETAKE
    window.PHOTO_CAPTURE_IN_PROGRESS = true;
    
    // NUCLEAR OPTION: Same protection as take_snapshot
    const originalFormSubmit = HTMLFormElement.prototype.submit;
    const originalDispatchEvent = Element.prototype.dispatchEvent;
    
    HTMLFormElement.prototype.submit = function() {
        console.log('🚫 BLOCKED HTMLFormElement.submit() during RETAKE!');
        return false;
    };
    
    Element.prototype.dispatchEvent = function(event) {
        if (window.PHOTO_CAPTURE_IN_PROGRESS && event.type === 'submit') {
            console.log('🚫 BLOCKED dispatchEvent(submit) during RETAKE!');
            event.preventDefault();
            event.stopImmediatePropagation();
            return false;
        }
        return originalDispatchEvent.call(this, event);
    };
    
    // Block document-level submits
    const submitBlocker = function(e) {
        if (window.PHOTO_CAPTURE_IN_PROGRESS) {
            console.log('🚫 BLOCKED document-level submit during RETAKE!');
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
    };
    
    document.addEventListener('submit', submitBlocker, true);
    document.addEventListener('submit', submitBlocker, false);
    
    // UI updates
    document.getElementById('results').innerHTML = '';
    document.getElementById('camera_status').innerHTML = '✅ Kamera aktif - Siap mengambil foto';
    document.getElementById('btn_capture').style.display = 'inline-block';
    document.getElementById('btn_retake').style.display = 'none';
    
    // Clear field values
    const fields = ['check_in_photo', 'check_in_photo_backup', 'check_in_photo_debug', 'check_in_photo_hidden_1', 'check_in_photo_hidden_2', 'check_in_photo_hidden_3'];
    fields.forEach(fieldName => {
        const field = document.querySelector('[name=' + fieldName + ']') || document.getElementById(fieldName);
        if (field) {
            field.value = '';
        }
    });
    
    // Clear localStorage
    localStorage.removeItem('absen_photo_backup');
    console.log('Cleared photo backup from localStorage');
    
    // RESTORE CAPABILITIES after safe delay
    setTimeout(() => {
        console.log('🔄 RESTORING FROM RETAKE...');
        
        window.PHOTO_CAPTURE_IN_PROGRESS = false;
        HTMLFormElement.prototype.submit = originalFormSubmit;
        Element.prototype.dispatchEvent = originalDispatchEvent;
        document.removeEventListener('submit', submitBlocker, true);
        document.removeEventListener('submit', submitBlocker, false);
        
        console.log('✅ RETAKE PROTECTION REMOVED - Ready for new photo!');
    }, 1000);
}

function debugFields() {
    console.log('=== MANUAL FIELD DEBUG ===');
    
    // Check all form fields
    const allFields = Array.from(document.querySelectorAll('input, textarea, select'));
    console.log('Total form fields found:', allFields.length);
    allFields.forEach((field, index) => {
        if (field.name) {
            console.log(`Field ${index}: name="${field.name}", type="${field.type}", id="${field.id}", value length=${field.value ? field.value.length : 0}`);
        }
    });
    
    // Check specific photo fields
    const photoFields = ['check_in_photo', 'check_in_photo_backup', 'check_in_photo_debug', 'check_in_photo_emergency'];
    photoFields.forEach(fieldName => {
        const field = document.querySelector('[name=' + fieldName + ']') || document.getElementById(fieldName);
        if (field) {
            console.log(`PHOTO FIELD ${fieldName}:`, {
                element: field,
                hasValue: !!field.value,
                valueLength: field.value ? field.value.length : 0,
                valuePreview: field.value ? field.value.substring(0, 100) : 'EMPTY'
            });
        } else {
            console.log(`PHOTO FIELD ${fieldName}: NOT FOUND`);
        }
    });
    
    // Check localStorage
    const backup = localStorage.getItem('absen_photo_backup');
    console.log('LocalStorage backup:', backup ? `EXISTS (${backup.length} chars)` : 'NOT FOUND');
    
    // Test manual field assignment
    const testData = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcU';
    const primaryField = document.querySelector('[name=check_in_photo]') || document.getElementById('check_in_photo_debug');
    if (primaryField) {
        console.log('Testing manual field assignment...');
        primaryField.value = testData;
        console.log('Test assignment result:', primaryField.value === testData ? 'SUCCESS' : 'FAILED');
    }
}

// EXTRA SAFETY: Override ALL possible submission methods globally
window.addEventListener('load', function() {
    // Block Livewire calls during photo capture
    const originalLivewire = window.Livewire;
    if (originalLivewire && originalLivewire.emit) {
        const originalEmit = originalLivewire.emit;
        originalLivewire.emit = function() {
            if (window.PHOTO_CAPTURE_IN_PROGRESS) {
                console.log('🚫 BLOCKED Livewire.emit during photo capture!');
                return false;
            }
            return originalEmit.apply(this, arguments);
        };
    }
    
    // Block any Ajax/Fetch requests during photo capture
    const originalFetch = window.fetch;
    window.fetch = function() {
        if (window.PHOTO_CAPTURE_IN_PROGRESS) {
            console.log('🚫 BLOCKED fetch request during photo capture!');
            return Promise.reject('Photo capture in progress');
        }
        return originalFetch.apply(this, arguments);
    };
    
    const originalXHR = window.XMLHttpRequest;
    window.XMLHttpRequest = function() {
        const xhr = new originalXHR();
        const originalSend = xhr.send;
        xhr.send = function() {
            if (window.PHOTO_CAPTURE_IN_PROGRESS) {
                console.log('🚫 BLOCKED XMLHttpRequest during photo capture!');
                return false;
            }
            return originalSend.apply(this, arguments);
        };
        return xhr;
    };
});

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initWebcam();
    
    // Aggressive form submit handler to ensure photo data is included
    function handleFormSubmit(e) {
        console.log('=== AGGRESSIVE FORM SUBMIT HANDLER ===');
        
        const backupPhoto = localStorage.getItem('absen_photo_backup');
        if (!backupPhoto) {
            console.error('❌ NO PHOTO DATA FOUND - Cannot submit without photo!');
            alert('Silakan ambil foto terlebih dahulu!');
            e.preventDefault();
            return false;
        }
        
        console.log('✅ Photo backup found, length:', backupPhoto.length);
        
        // FORCE inject to all possible field types and locations
        const fieldTargets = [
            { selector: '[name="check_in_photo"]', name: 'check_in_photo' },
            { selector: '#check_in_photo_debug', name: 'check_in_photo_debug' },
            { selector: '[name="check_in_photo_backup"]', name: 'check_in_photo_backup' },
            { selector: '#check_in_photo_backup', name: 'check_in_photo_backup_id' }
        ];
        
        let successCount = 0;
        fieldTargets.forEach(target => {
            const field = document.querySelector(target.selector);
            if (field) {
                field.value = backupPhoto;
                console.log(`✅ INJECTED to ${target.name}: ${field.value.length} chars`);
                successCount++;
            } else {
                console.log(`❌ Field not found: ${target.name}`);
            }
        });
        
        // Create multiple emergency fields to guarantee data transmission
        for (let i = 1; i <= 3; i++) {
            const emergencyField = document.createElement('input');
            emergencyField.type = 'hidden';
            emergencyField.name = `check_in_photo_emergency_${i}`;
            emergencyField.value = backupPhoto;
            
            // Add to both form and body for maximum coverage
            if (e.target.tagName === 'FORM') {
                e.target.appendChild(emergencyField);
            } else {
                document.body.appendChild(emergencyField);
            }
            console.log(`✅ Emergency field ${i} created with ${backupPhoto.length} chars`);
        }
        
        // Final verification before allowing submit
        setTimeout(() => {
            console.log('=== PRE-SUBMIT VERIFICATION ===');
            const allPossibleFields = document.querySelectorAll('input[name*="photo"], textarea[name*="photo"], input[name*="emergency"]');
            let hasPhotoData = false;
            
            allPossibleFields.forEach(field => {
                if (field.value && field.value.length > 1000) {
                    console.log(`✅ Verified field ${field.name}: ${field.value.length} chars`);
                    hasPhotoData = true;
                }
            });
            
            if (!hasPhotoData) {
                console.error('❌ CRITICAL: No photo data found in any field after injection!');
            } else {
                console.log('✅ SUCCESS: Photo data confirmed in form fields');
            }
        }, 50);
        
        console.log(`Total fields injected: ${successCount}, Emergency fields: 3`);
        return true; // Allow submission to proceed
    }
    
    // Attach to all possible form events
    document.addEventListener('submit', handleFormSubmit, true);
    document.addEventListener('submit', handleFormSubmit, false);
    
    // Monitor Livewire requests and preserve photo data
    document.addEventListener('livewire:before-request', function(e) {
        console.log('=== LIVEWIRE BEFORE REQUEST ===');
        
        // Inject photo data before any Livewire request
        const backupPhoto = localStorage.getItem('absen_photo_backup');
        if (backupPhoto) {
            const fields = ['check_in_photo', 'check_in_photo_backup', 'check_in_photo_debug', 'check_in_photo_hidden_1', 'check_in_photo_hidden_2', 'check_in_photo_hidden_3'];
            fields.forEach(fieldName => {
                const field = document.querySelector('[name=' + fieldName + ']') || document.getElementById(fieldName);
                if (field && !field.value) {
                    field.value = backupPhoto;
                    console.log(`Pre-request injection to ${fieldName}`);
                }
            });
        }
    });
    
    document.addEventListener('livewire:update', function(e) {
        console.log('=== LIVEWIRE UPDATE DETECTED ===');
        
        // Re-inject photo data after Livewire updates
        const backupPhoto = localStorage.getItem('absen_photo_backup');
        if (backupPhoto) {
            const fields = ['check_in_photo', 'check_in_photo_backup', 'check_in_photo_debug', 'check_in_photo_hidden_1', 'check_in_photo_hidden_2', 'check_in_photo_hidden_3'];
            fields.forEach(fieldName => {
                const field = document.querySelector('[name=' + fieldName + ']') || document.getElementById(fieldName);
                if (field && !field.value) {
                    field.value = backupPhoto;
                    console.log(`Post-update injection to ${fieldName}`);
                }
            });
        }
    });
});
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
            📸 Ambil Foto
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