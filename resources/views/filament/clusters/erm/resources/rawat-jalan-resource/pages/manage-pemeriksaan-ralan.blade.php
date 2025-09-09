<div class="space-y-6">
    {{-- Form Section --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-content p-6">
            {{-- Action Buttons --}}
            <div class="flex gap-3 mb-6">
                <x-filament::button
                    type="button"
                    wire:click="simpanPemeriksaan"
                    size="sm"
                >
                    <x-slot name="icon">
                        <svg class="fi-btn-icon h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L7.53 10.03a.75.75 0 00-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </x-slot>
                    {{ $editingId ? 'Update Pemeriksaan' : 'Simpan Pemeriksaan' }}
                </x-filament::button>
                
                <x-filament::button
                    type="button"
                    color="gray"
                    wire:click="resetForm"
                    size="sm"
                >
                    <x-slot name="icon">
                        <svg class="fi-btn-icon h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd" />
                        </svg>
                    </x-slot>
                    Reset Form
                </x-filament::button>
                
                @if($editingId)
                    <div class="flex items-center px-3 py-1 bg-amber-50 text-amber-700 rounded-md text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Mode Edit - Data akan diupdate
                    </div>
                @endif
            </div>
            
            {{-- Form --}}
            {{ $this->form }}
        </div>
    </div>

    {{-- Table Section --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header flex flex-col gap-3 p-6 sm:flex-row sm:items-center">
            <div class="grid gap-y-1">
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Riwayat Pemeriksaan
                </h3>
                <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                    Daftar semua pemeriksaan yang telah dilakukan untuk pasien ini
                </p>
            </div>
        </div>
        
        <div class="fi-section-content">
            {{ $this->table }}
        </div>
    </div>
</div>

{{-- Custom Styles for Mobile Responsive --}}
<style>
@media (max-width: 768px) {
    .fi-ta-table {
        font-size: 0.875rem;
    }
    
    .fi-ta-cell {
        padding: 0.5rem 0.25rem;
    }
    
    .fi-ta-header-cell {
        padding: 0.75rem 0.25rem;
    }
    
    /* Hide less important columns on mobile */
    .fi-ta-table .fi-ta-col-respirasi,
    .fi-ta-table .fi-ta-col-spo2,
    .fi-ta-table .fi-ta-col-bb-tb {
        display: none;
    }
}

@media (max-width: 640px) {
    /* Hide even more columns on very small screens */
    .fi-ta-table .fi-ta-col-penilaian,
    .fi-ta-table .fi-ta-col-keluhan {
        display: none;
    }
}

/* Edit mode styling */
.edit-mode-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}
</style>

{{-- JavaScript for better UX --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto scroll to form when editing
    window.addEventListener('edit-mode-activated', function() {
        document.querySelector('.fi-section').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    });
    
    // Confirmation before reset when form has data
    document.addEventListener('click', function(e) {
        if (e.target.closest('[wire\\:click="resetForm"]')) {
            const formInputs = document.querySelectorAll('input[wire\\:model], textarea[wire\\:model], select[wire\\:model]');
            let hasData = false;
            
            formInputs.forEach(input => {
                if (input.value && input.value.trim() !== '') {
                    hasData = true;
                }
            });
            
            if (hasData) {
                if (!confirm('Form berisi data. Yakin ingin mereset?')) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }
        }
    });
});
</script>