<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pemeriksaan Rawat Jalan - WebKhanza</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    <x-livewire-styles :theme="request()->cookie('theme', 'light')" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Filament Styles -->
    @filamentStyles
    @livewireStyles
</head>
<body class="min-h-full bg-gray-50 dark:bg-gray-900 livewire-component">
    <div class="min-h-full">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                                <i class="fas fa-stethoscope text-primary-600 dark:text-primary-400 mr-2"></i>
                                Pemeriksaan Rawat Jalan
                            </h1>
                        </div>
                    </div>

                    <!-- Patient Info -->
                    @if(isset($record))
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="livewire-card p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-user-injured text-blue-600 dark:text-blue-400"></i>
                                <div>
                                    <div class="font-semibold text-blue-900 dark:text-blue-100">{{ $record->pasien->nm_pasien ?? 'N/A' }}</div>
                                    <div class="text-blue-700 dark:text-blue-300">No. RM: {{ $record->no_rkm_medis ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="space-y-6">

                <!-- Form Section -->
                <div class="livewire-card">
                    <div class="livewire-card-header">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <i class="fas fa-edit text-primary-600 dark:text-primary-400 mr-2"></i>
                                    Form Pemeriksaan SOAPIE
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Subjective, Objective, Assessment, Plan, Intervention, Evaluation
                                </p>
                            </div>

                            @if($editingId)
                                <div class="livewire-alert livewire-alert-warning px-3 py-2 rounded-md flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span class="text-sm font-medium">Mode Edit - Data akan diupdate</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="livewire-card-body">
                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3 mb-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <button type="button"
                                    wire:click="simpanPemeriksaan"
                                    class="livewire-btn-primary flex items-center space-x-2">
                                <i class="fas fa-save"></i>
                                <span>{{ $editingId ? 'Update Pemeriksaan' : 'Simpan Pemeriksaan' }}</span>
                            </button>

                            <button type="button"
                                    wire:click="resetForm"
                                    class="livewire-btn-secondary flex items-center space-x-2"
                                    onclick="return confirmReset()">
                                <i class="fas fa-undo"></i>
                                <span>Reset Form</span>
                            </button>

                            @if($editingId)
                                <button type="button"
                                        wire:click="cancelEdit"
                                        class="livewire-btn-secondary border-orange-300 text-orange-700 hover:bg-orange-50 dark:border-orange-600 dark:text-orange-300 dark:hover:bg-orange-900/20 flex items-center space-x-2">
                                    <i class="fas fa-times"></i>
                                    <span>Batalkan Edit</span>
                                </button>
                            @endif
                        </div>

                        <!-- Form -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                            {{ $this->form }}
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="livewire-card">
                    <div class="livewire-card-header">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <i class="fas fa-history text-primary-600 dark:text-primary-400 mr-2"></i>
                                    Riwayat Pemeriksaan
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Daftar semua pemeriksaan yang telah dilakukan untuk pasien ini
                                </p>
                            </div>

                            <!-- Quick Stats -->
                            <div class="hidden md:flex items-center space-x-4 text-sm">
                                <div class="livewire-card px-3 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-chart-line text-green-600 dark:text-green-400"></i>
                                        <div>
                                            <div class="font-semibold text-green-900 dark:text-green-100">Total</div>
                                            <div class="text-green-700 dark:text-green-300">{{ $this->getTableRecords()->count() }} Records</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="livewire-card-body p-0">
                        <div class="overflow-hidden">
                            {{ $this->table }}
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Enhanced Mobile Responsive Styles -->
    <style>
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .fi-ta-table {
                font-size: 0.75rem;
            }

            .fi-ta-cell, .fi-ta-header-cell {
                padding: 0.5rem 0.25rem;
            }

            /* Progressive disclosure - hide less critical columns */
            .fi-ta-table .fi-ta-col-respirasi,
            .fi-ta-table .fi-ta-col-spo2,
            .fi-ta-table .fi-ta-col-bb-tb,
            .fi-ta-table .fi-ta-col-lingkar_perut,
            .fi-ta-table .fi-ta-col-alergi {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .fi-ta-table .fi-ta-col-penilaian,
            .fi-ta-table .fi-ta-col-instruksi,
            .fi-ta-table .fi-ta-col-evaluasi {
                display: none;
            }

            /* Compact header on mobile */
            .max-w-7xl h-16 {
                height: auto;
                min-height: 3rem;
                padding: 0.5rem 0;
            }
        }

        /* Edit mode animations */
        @keyframes editPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .edit-mode .livewire-alert-warning {
            animation: editPulse 2s infinite;
        }

        /* Enhanced table styling */
        .fi-ta-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .fi-ta-table th {
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--bg-gray-50);
            backdrop-filter: blur(8px);
        }

        /* Loading states */
        .livewire-loading {
            position: relative;
        }

        .livewire-loading::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }

        /* Custom scrollbar */
        .overflow-auto::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .overflow-auto::-webkit-scrollbar-track {
            background: var(--bg-gray-100);
            border-radius: 3px;
        }

        .overflow-auto::-webkit-scrollbar-thumb {
            background: var(--border-gray-300);
            border-radius: 3px;
        }

        .overflow-auto::-webkit-scrollbar-thumb:hover {
            background: var(--border-gray-400);
        }
    </style>

    <!-- Enhanced JavaScript for better UX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit mode management
            let isEditMode = {{ $editingId ? 'true' : 'false' }};

            if (isEditMode) {
                document.body.classList.add('edit-mode');

                // Auto scroll to form when editing
                setTimeout(() => {
                    document.querySelector('.livewire-card').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            }

            // Listen for edit mode changes
            window.addEventListener('edit-mode-activated', function() {
                document.body.classList.add('edit-mode');
                document.querySelector('.livewire-card').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            window.addEventListener('edit-mode-deactivated', function() {
                document.body.classList.remove('edit-mode');
            });
        });

        // Enhanced reset confirmation
        function confirmReset() {
            const formInputs = document.querySelectorAll('input[wire\\:model], textarea[wire\\:model], select[wire\\:model]');
            let hasData = false;

            formInputs.forEach(input => {
                if (input.value && input.value.trim() !== '') {
                    hasData = true;
                }
            });

            if (hasData) {
                return confirm('⚠️ Form berisi data yang belum disimpan.\\n\\nYakin ingin mereset semua data?');
            }
            return true;
        }

        // Auto-save draft functionality (optional)
        let autoSaveTimer;
        function scheduleDraftSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Could implement draft saving here
                console.log('Auto-save draft...');
            }, 30000); // Save draft every 30 seconds
        }

        // Listen for form changes to trigger auto-save
        document.addEventListener('input', function(e) {
            if (e.target.hasAttribute('wire:model')) {
                scheduleDraftSave();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                const saveButton = document.querySelector('[wire\\:click="simpanPemeriksaan"]');
                if (saveButton) {
                    saveButton.click();
                }
            }

            // Ctrl/Cmd + R to reset (with confirmation)
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                if (confirmReset()) {
                    const resetButton = document.querySelector('[wire\\:click="resetForm"]');
                    if (resetButton) {
                        resetButton.click();
                    }
                }
            }

            // ESC to cancel edit
            if (e.key === 'Escape' && document.body.classList.contains('edit-mode')) {
                const cancelButton = document.querySelector('[wire\\:click="cancelEdit"]');
                if (cancelButton) {
                    cancelButton.click();
                }
            }
        });

        // Loading indicator for Livewire actions
        document.addEventListener('livewire:init', function() {
            Livewire.hook('morph.added', ({ el }) => {
                // Add loading class during updates
                el.classList.add('livewire-loading');
            });

            Livewire.hook('morph.updated', ({ el }) => {
                // Remove loading class after updates
                el.classList.remove('livewire-loading');
            });
        });
    </script>

    <!-- Filament Scripts -->
    @filamentScripts
    @livewireScripts
</body>
</html>