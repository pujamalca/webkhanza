<?php

namespace App\Livewire;

use App\Models\PemeriksaanRalan;
use App\Models\Pegawai;
use App\Models\SoapieTemplate;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;

class PemeriksaanRalanForm extends Component
{
    public string $noRawat;
    public string $noRkmMedis;
    public $patientData = [];
    
    #[Validate('required|date')]
    public $tgl_perawatan;
    
    #[Validate('required')]
    public $jam_rawat;
    
    #[Validate('nullable|numeric')]
    public $suhu_tubuh;
    
    #[Validate('nullable')]
    public $tensi;
    
    #[Validate('required|numeric|min:30|max:160')]
    public $nadi;
    
    #[Validate('required|numeric|min:5|max:70')]
    public $respirasi;
    
    #[Validate('nullable|numeric|min:0|max:100')]
    public $spo2;
    
    #[Validate('required|numeric|min:30|max:250')]
    public $tinggi;
    
    #[Validate('required|numeric|min:2|max:300')]
    public $berat;
    
    #[Validate('nullable')]
    public $gcs;
    
    #[Validate('nullable')]
    public $kesadaran;
    
    // SOAP fields
    #[Validate('required|min:4')]
    public $keluhan; // Subjective
    
    #[Validate('required|min:10')]
    public $pemeriksaan; // Objective
    
    #[Validate('nullable')]
    public $penilaian; // Assessment
    
    #[Validate('required|min:4')]
    public $rtl; // Plan
    
    #[Validate('nullable')]
    public $instruksi; // Intervention
    
    #[Validate('nullable')]
    public $evaluasi; // Evaluation
    
    #[Validate('nullable|max:50')]
    public $alergi;
    
    #[Validate('nullable|numeric')]
    public $lingkar_perut;

    #[Validate('required')]
    public $nip;

    // Display name for petugas
    public $namaPetugas;

    // History data
    public $riwayatPemeriksaan = [];
    
    // Edit mode
    public $editingId = null;
    public $originalNoRawat = null;
    public $originalTglPerawatan = null;
    public $originalJamRawat = null;
    
    // Display limit
    public $perPage = 2;

    // History pagination
    public $historyPage = 1;
    public $historyPerPage = 2;
    public $totalHistory = 0;

    // Pegawai list
    public $pegawaiList = [];
    public $isAdmin = false;

    // Template functionality
    public $soapieTemplates = [];
    public $showTemplateModal = false;
    public $selectedTemplate = null;
    public $saveToTemplate = false;
    public $showCreateTemplate = false;

    // Template pagination
    public $templatePage = 1;
    public $templatePerPage = 5;
    public $totalTemplates = 0;

    // New template fields
    public $newTemplateName = '';
    public $newTemplateCategory = '';
    public $newTemplateDescription = '';
    public $newTemplateIsPublic = false;

    // Template SOAPIE fields
    public $newTemplateSubjective = '';
    public $newTemplateObjective = '';
    public $newTemplateAssessment = '';
    public $newTemplatePlan = '';
    public $newTemplateIntervention = '';
    public $newTemplateEvaluation = '';

    public function mount(string $noRawat): void
    {
        \Log::info('PemeriksaanRalanForm mounted', [
            'noRawat' => $noRawat,
            'class' => get_class($this)
        ]);
        $this->noRawat = $noRawat;

        // Get no_rkm_medis and patient data from reg_periksa and pasien
        $regPeriksa = \DB::table('reg_periksa')
            ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->select(
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.umur',
                'pasien.tgl_lahir',
                'pasien.alamat'
            )
            ->first();

        if ($regPeriksa) {
            $this->noRkmMedis = $regPeriksa->no_rkm_medis;
            $this->patientData = [
                'no_rkm_medis' => $regPeriksa->no_rkm_medis,
                'nama' => $regPeriksa->nm_pasien,
                'jk' => $regPeriksa->jk === 'L' ? 'Laki-laki' : 'Perempuan',
                'umur' => $regPeriksa->umur,
                'tgl_lahir' => $regPeriksa->tgl_lahir,
                'alamat' => $regPeriksa->alamat
            ];
        } else {
            // Fallback if no reg_periksa found
            $this->noRkmMedis = '';
            $this->patientData = [];
        }
        
        // Check user permissions
        $this->isAdmin = auth()->user()->hasRole(['super_admin', 'admin']) ||
                        auth()->user()->hasPermissionTo('manage_all_examinations');
        
        // Load pegawai list for admin - using users table
        if ($this->isAdmin) {
            $this->pegawaiList = \DB::table('users')
                ->whereNotNull('username')
                ->orderBy('name')
                ->pluck('name', 'username')
                ->toArray();
        }
        
        $this->resetForm();
        $this->loadRiwayat();
        $this->loadTemplates();
    }
    
    public function simpanPemeriksaan()
    {
        // Check permission to create/edit examinations
        if ($this->editingId) {
            if (!auth()->user()->hasPermissionTo('rawat_jalan_edit') &&
                !auth()->user()->hasPermissionTo('manage_all_examinations')) {
                Notification::make()
                    ->title('Akses Ditolak')
                    ->body('Anda tidak memiliki izin untuk mengedit pemeriksaan SOAPIE')
                    ->danger()
                    ->send();
                return;
            }
        } else {
            if (!auth()->user()->hasPermissionTo('rawat_jalan_create')) {
                Notification::make()
                    ->title('Akses Ditolak')
                    ->body('Anda tidak memiliki izin untuk membuat pemeriksaan SOAPIE')
                    ->danger()
                    ->send();
                return;
            }
        }

        \Log::info('simpanPemeriksaan method called', [
            'noRawat' => $this->noRawat,
            'tgl_perawatan' => $this->tgl_perawatan,
            'jam_rawat' => $this->jam_rawat
        ]);

        $this->validate();

        $data = [
            'no_rawat' => $this->noRawat,
            'tgl_perawatan' => $this->tgl_perawatan,
            'jam_rawat' => $this->jam_rawat,
            'suhu_tubuh' => $this->suhu_tubuh,
            'tensi' => $this->tensi,
            'nadi' => $this->nadi,
            'respirasi' => $this->respirasi,
            'spo2' => $this->spo2,
            'tinggi' => $this->tinggi,
            'berat' => $this->berat,
            'gcs' => $this->gcs,
            'kesadaran' => $this->kesadaran,
            'keluhan' => $this->keluhan,
            'pemeriksaan' => $this->pemeriksaan,
            'penilaian' => $this->penilaian,
            'alergi' => $this->alergi,
            'lingkar_perut' => $this->lingkar_perut,
            'rtl' => $this->rtl,
            'instruksi' => $this->instruksi,
            'evaluasi' => $this->evaluasi,
            'nip' => $this->nip,
        ];

        if ($this->editingId) {
            // Update existing record using original coordinates
            try {
                $updated = PemeriksaanRalan::where('no_rawat', $this->originalNoRawat)
                    ->where('tgl_perawatan', $this->originalTglPerawatan)
                    ->where('jam_rawat', $this->originalJamRawat)
                    ->update($data);

                if ($updated) {
                    $message = 'Pemeriksaan SOAP berhasil diupdate';
                    // Note: data will keep the current no_rawat from the examination being edited
                } else {
                    // If no rows updated, create new record
                    PemeriksaanRalan::create($data);
                    $message = 'Pemeriksaan SOAP berhasil disimpan sebagai record baru';
                }
            } catch (\Exception $e) {
                \Log::error('Update failed, creating new record', ['error' => $e->getMessage()]);
                // If update fails, try to create new
                PemeriksaanRalan::create($data);
                $message = 'Pemeriksaan SOAP berhasil disimpan';
            }
        } else {
            // Create new record
            PemeriksaanRalan::create($data);
            $message = 'Pemeriksaan SOAP berhasil disimpan';
        }

        // Save to template if checkbox is checked
        if ($this->saveToTemplate && !empty($this->keluhan) && !empty($this->pemeriksaan) && !empty($this->rtl)) {
            $this->saveCurrentAsTemplate();
        }

        $this->resetForm();
        // Reload latest data
        $this->loadRiwayat();

        Notification::make()
            ->title($message)
            ->success()
            ->send();
    }

    public function refreshDateTime()
    {
        \Log::info('refreshDateTime method called');
        $this->tgl_perawatan = now()->format('Y-m-d');
        $this->jam_rawat = now()->format('H:i');
    }
    
    public function resetForm(): void
    {
        $this->editingId = null;
        $this->originalNoRawat = null;
        $this->originalTglPerawatan = null;
        $this->originalJamRawat = null;
        $this->refreshDateTime();
        $this->suhu_tubuh = '';
        $this->tensi = '';
        $this->nadi = '';
        $this->respirasi = '';
        $this->spo2 = '';
        $this->tinggi = '';
        $this->berat = '';
        $this->gcs = '';
        $this->kesadaran = 'Compos Mentis';
        $this->keluhan = '';
        $this->pemeriksaan = '';
        $this->penilaian = '';
        $this->alergi = '';
        $this->lingkar_perut = '';
        $this->rtl = '';
        $this->instruksi = '';
        $this->evaluasi = '';

        // Reset template flags
        $this->saveToTemplate = false;
        $this->showCreateTemplate = false;

        // Set NIP and nama petugas based on role
        if ($this->isAdmin) {
            $this->nip = ''; // Admin can choose
            $this->namaPetugas = '';
        } else {
            $this->nip = auth()->user()->username ?? '-';
            $this->namaPetugas = auth()->user()->name ?? '-';
        }
    }

    public function updatedNip($value)
    {
        // Update nama petugas when NIP changes (for admin)
        if ($this->isAdmin && !empty($value)) {
            $user = \DB::table('users')->where('username', $value)->first();
            $this->namaPetugas = $user ? $user->name : $value;
        }
    }

    public function hapusPemeriksaan($tglPerawatan, $jamRawat, $sourceNoRawat = null)
    {
        try {
            // Use the provided source no_rawat or fall back to current no_rawat
            $sourceNoRawat = $sourceNoRawat ?: $this->noRawat;


            $pemeriksaan = PemeriksaanRalan::where('no_rawat', $sourceNoRawat)
                ->where('tgl_perawatan', $tglPerawatan)
                ->where('jam_rawat', $jamRawat)
                ->first();

            if (!$pemeriksaan) {
                Notification::make()
                    ->title('Data tidak ditemukan')
                    ->warning()
                    ->send();
                return;
            }

            // Authorization check: only creator or users with permission can delete
            $currentUserNip = auth()->user()->username ?? '-';
            $canDeleteAll = auth()->user()->hasPermissionTo('manage_all_examinations');
            $canDeleteOwn = auth()->user()->hasPermissionTo('rawat_jalan_delete');

            if (!$canDeleteAll && (!$canDeleteOwn || $pemeriksaan->nip !== $currentUserNip)) {
                Notification::make()
                    ->title('Akses Ditolak')
                    ->body('Anda tidak memiliki izin untuk menghapus pemeriksaan ini')
                    ->danger()
                    ->send();
                return;
            }


            $pemeriksaan->delete();

            // Reload latest data
            $this->loadRiwayat();

            Notification::make()
                ->title('Pemeriksaan berhasil dihapus')
                ->success()
                ->send();

        } catch (\Exception $e) {
            \Log::error('Hapus pemeriksaan error', ['error' => $e->getMessage()]);
            Notification::make()
                ->title('Error menghapus data: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function editPemeriksaan($tglPerawatan, $jamRawat, $sourceNoRawat = null)
    {
        try {
            // Use the provided source no_rawat or fall back to current no_rawat
            $sourceNoRawat = $sourceNoRawat ?: $this->noRawat;

            \Log::info('EditPemeriksaan called', [
                'currentNoRawat' => $this->noRawat,
                'sourceNoRawat' => $sourceNoRawat,
                'tglPerawatan' => $tglPerawatan,
                'jamRawat' => $jamRawat
            ]);

            $pemeriksaan = PemeriksaanRalan::where('no_rawat', $sourceNoRawat)
                ->where('tgl_perawatan', $tglPerawatan)
                ->where('jam_rawat', $jamRawat)
                ->first();

            if (!$pemeriksaan) {
                Notification::make()
                    ->title('Data tidak ditemukan')
                    ->warning()
                    ->send();
                return;
            }

            // Authorization check: only creator or users with permission can edit
            $currentUserNip = auth()->user()->pegawai->nik ?? '-';
            $canEditAll = auth()->user()->hasPermissionTo('manage_all_examinations');
            $canEditOwn = auth()->user()->hasPermissionTo('rawat_jalan_edit');

            if (!$canEditAll && (!$canEditOwn || $pemeriksaan->nip !== $currentUserNip)) {
                Notification::make()
                    ->title('Akses Ditolak')
                    ->body('Anda tidak memiliki izin untuk mengedit pemeriksaan ini')
                    ->danger()
                    ->send();
                return;
            }

            \Log::info('Pemeriksaan found and authorized', ['found' => 'yes', 'canEdit' => 'yes']);

            if ($pemeriksaan) {
                $rawAttrs = $pemeriksaan->getAttributes();

                // Store original examination details for updating
                $this->originalNoRawat = $rawAttrs['no_rawat'];
                $this->originalTglPerawatan = $rawAttrs['tgl_perawatan'];
                $this->originalJamRawat = $rawAttrs['jam_rawat'];

                // Update current no_rawat and patient data for editing
                $this->noRawat = $rawAttrs['no_rawat'];

                // Load patient data for the examination being edited
                $regPeriksa = \DB::table('reg_periksa')
                    ->leftJoin('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->where('reg_periksa.no_rawat', $rawAttrs['no_rawat'])
                    ->select(
                        'reg_periksa.no_rkm_medis',
                        'pasien.nm_pasien',
                        'pasien.jk',
                        'pasien.umur',
                        'pasien.tgl_lahir',
                        'pasien.alamat'
                    )
                    ->first();

                if ($regPeriksa) {
                    $this->noRkmMedis = $regPeriksa->no_rkm_medis;
                    $this->patientData = [
                        'no_rkm_medis' => $regPeriksa->no_rkm_medis,
                        'nama' => $regPeriksa->nm_pasien,
                        'jk' => $regPeriksa->jk === 'L' ? 'Laki-laki' : 'Perempuan',
                        'umur' => $regPeriksa->umur,
                        'tgl_lahir' => $regPeriksa->tgl_lahir,
                        'alamat' => $regPeriksa->alamat
                    ];
                }

                $this->editingId = $rawAttrs['tgl_perawatan'] . '-' . $rawAttrs['jam_rawat'] . '-' . $rawAttrs['no_rawat'];
                $this->tgl_perawatan = $rawAttrs['tgl_perawatan'];
                $this->jam_rawat = substr($rawAttrs['jam_rawat'], 0, 5); // Format HH:MM
                $this->suhu_tubuh = $pemeriksaan->suhu_tubuh ?? '';
                $this->tensi = $pemeriksaan->tensi ?? '';
                $this->nadi = $pemeriksaan->nadi ?? '';
                $this->respirasi = $pemeriksaan->respirasi ?? '';
                $this->spo2 = $pemeriksaan->spo2 ?? '';
                $this->tinggi = $pemeriksaan->tinggi ?? '';
                $this->berat = $pemeriksaan->berat ?? '';
                $this->gcs = $pemeriksaan->gcs ?? '';
                $this->kesadaran = $pemeriksaan->kesadaran ?? '';
                $this->keluhan = $pemeriksaan->keluhan ?? '';
                $this->pemeriksaan = $pemeriksaan->pemeriksaan ?? '';
                $this->penilaian = $pemeriksaan->penilaian ?? '';
                $this->alergi = $pemeriksaan->alergi ?? '';
                $this->lingkar_perut = $pemeriksaan->lingkar_perut ?? '';
                $this->rtl = $pemeriksaan->rtl ?? '';
                $this->instruksi = $pemeriksaan->instruksi ?? '';
                $this->evaluasi = $pemeriksaan->evaluasi ?? '';
                $this->nip = $pemeriksaan->nip ?? '';

                // Set nama petugas for display
                if (!empty($this->nip)) {
                    $user = \DB::table('users')->where('username', $this->nip)->first();
                    $this->namaPetugas = $user ? $user->name : $this->nip;
                }

                \Log::info('Data loaded', ['keluhan' => $this->keluhan]);
                
                Notification::make()
                    ->title('Data berhasil dimuat untuk edit')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Data tidak ditemukan')
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            \Log::error('Edit pemeriksaan error', ['error' => $e->getMessage()]);
            Notification::make()
                ->title('Error loading data: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function selectExamination($tglPerawatan, $jamRawat, $noRawat)
    {
        try {
            \Log::info('SelectExamination called', [
                'targetNoRawat' => $noRawat,
                'tglPerawatan' => $tglPerawatan,
                'jamRawat' => $jamRawat
            ]);

            $pemeriksaan = PemeriksaanRalan::where('no_rawat', $noRawat)
                ->where('tgl_perawatan', $tglPerawatan)
                ->where('jam_rawat', $jamRawat)
                ->first();

            if ($pemeriksaan) {
                // Fill form with selected examination data (without entering edit mode)
                $this->editingId = null; // Clear edit mode
                $this->tgl_perawatan = now()->format('Y-m-d'); // Keep current date
                $this->jam_rawat = now()->format('H:i'); // Keep current time

                // Fill TTV data
                $this->suhu_tubuh = $pemeriksaan->suhu_tubuh ?? '';
                $this->tensi = $pemeriksaan->tensi ?? '';
                $this->nadi = $pemeriksaan->nadi ?? '';
                $this->respirasi = $pemeriksaan->respirasi ?? '';
                $this->spo2 = $pemeriksaan->spo2 ?? '';
                $this->tinggi = $pemeriksaan->tinggi ?? '';
                $this->berat = $pemeriksaan->berat ?? '';
                $this->gcs = $pemeriksaan->gcs ?? '';
                $this->kesadaran = $pemeriksaan->kesadaran ?? 'Compos Mentis';
                $this->alergi = $pemeriksaan->alergi ?? '';
                $this->lingkar_perut = $pemeriksaan->lingkar_perut ?? '';

                // Fill SOAPIE data
                $this->keluhan = $pemeriksaan->keluhan ?? '';
                $this->pemeriksaan = $pemeriksaan->pemeriksaan ?? '';
                $this->penilaian = $pemeriksaan->penilaian ?? '';
                $this->rtl = $pemeriksaan->rtl ?? '';
                $this->instruksi = $pemeriksaan->instruksi ?? '';
                $this->evaluasi = $pemeriksaan->evaluasi ?? '';

                // Keep current NIP and nama petugas
                if (!$this->isAdmin) {
                    $this->nip = auth()->user()->username ?? '-';
                    $this->namaPetugas = auth()->user()->name ?? '-';
                }

                $examDate = \Carbon\Carbon::parse($pemeriksaan->tgl_perawatan)->format('d/m/Y');
                $examTime = substr($pemeriksaan->jam_rawat, 0, 5);

                Notification::make()
                    ->title('Data pemeriksaan berhasil dipilih')
                    ->body("Data TTV dan SOAPIE dari {$noRawat} tanggal {$examDate} jam {$examTime} telah diisi ke form")
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Data tidak ditemukan')
                    ->warning()
                    ->send();
            }
        } catch (\Exception $e) {
            \Log::error('Select examination error', ['error' => $e->getMessage()]);
            Notification::make()
                ->title('Error loading data: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function loadRiwayat(): void
    {
        if (empty($this->noRkmMedis)) {
            $this->riwayatPemeriksaan = [];
            $this->totalHistory = 0;
            return;
        }

        // Get all no_rawat for this patient (no_rkm_medis)
        $noRawatList = \DB::table('reg_periksa')
            ->where('no_rkm_medis', $this->noRkmMedis)
            ->pluck('no_rawat')
            ->toArray();

        if (empty($noRawatList)) {
            $this->riwayatPemeriksaan = [];
            $this->totalHistory = 0;
            return;
        }

        // Count total examinations for this patient
        $this->totalHistory = PemeriksaanRalan::whereIn('no_rawat', $noRawatList)->count();

        // Get paginated examination data for all visits of this patient with user name
        // Fallback to pegawai table if user not found
        $data = \DB::table('pemeriksaan_ralan')
            ->leftJoin('users', 'pemeriksaan_ralan.nip', '=', 'users.username')
            ->leftJoin('pegawai', 'pemeriksaan_ralan.nip', '=', 'pegawai.nik')
            ->whereIn('pemeriksaan_ralan.no_rawat', $noRawatList)
            ->select(
                'pemeriksaan_ralan.*',
                \DB::raw('COALESCE(users.name, pegawai.nama, pemeriksaan_ralan.nip) as petugas_nama')
            )
            ->orderBy('pemeriksaan_ralan.tgl_perawatan', 'desc')
            ->orderBy('pemeriksaan_ralan.jam_rawat', 'desc')
            ->skip(($this->historyPage - 1) * $this->historyPerPage)
            ->take($this->historyPerPage)
            ->get();

        $this->riwayatPemeriksaan = $data->map(function($item) {
            $array = (array) $item;
            // Keep raw values for edit function - use raw database values
            $array['tgl_perawatan_raw'] = $item->tgl_perawatan;
            $array['jam_rawat_raw'] = $item->jam_rawat;
            // Add petugas data in expected format for the view
            $array['petugas'] = ['nama' => $item->petugas_nama];
            return $array;
        })->toArray();
    }
    
    public $totalRecords = 0;
    
    public function loadTemplates(): void
    {
        // Check permission to view templates
        if (!auth()->user()->hasPermissionTo('view_soapie_templates')) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda tidak memiliki izin untuk melihat template SOAPIE')
                ->danger()
                ->send();
            return;
        }

        $currentNip = $this->nip ?: (auth()->user()->username ?? '-');

        $query = SoapieTemplate::forUser($currentNip)
            ->orderBy('is_public', 'asc')
            ->orderBy('nama_template', 'asc');

        // Get total count
        $this->totalTemplates = $query->count();

        // Get paginated data
        $this->soapieTemplates = $query
            ->skip(($this->templatePage - 1) * $this->templatePerPage)
            ->take($this->templatePerPage)
            ->get()
            ->toArray();
    }

    public function openTemplateModal()
    {
        $this->showTemplateModal = true;
        $this->loadTemplates();
    }

    public function closeTemplateModal()
    {
        $this->showTemplateModal = false;
        $this->selectedTemplate = null;
        $this->templatePage = 1; // Reset pagination when closing modal
    }

    public function nextTemplatePage()
    {
        $maxPage = ceil($this->totalTemplates / $this->templatePerPage);
        if ($this->templatePage < $maxPage) {
            $this->templatePage++;
            $this->loadTemplates();
        }
    }

    public function previousTemplatePage()
    {
        if ($this->templatePage > 1) {
            $this->templatePage--;
            $this->loadTemplates();
        }
    }

    public function goToTemplatePage($page)
    {
        $maxPage = ceil($this->totalTemplates / $this->templatePerPage);
        if ($page >= 1 && $page <= $maxPage) {
            $this->templatePage = $page;
            $this->loadTemplates();
        }
    }

    // History pagination methods
    public function nextHistoryPage()
    {
        $maxPage = ceil($this->totalHistory / $this->historyPerPage);
        if ($this->historyPage < $maxPage) {
            $this->historyPage++;
            $this->loadRiwayat();
        }
    }

    public function previousHistoryPage()
    {
        if ($this->historyPage > 1) {
            $this->historyPage--;
            $this->loadRiwayat();
        }
    }

    public function goToHistoryPage($page)
    {
        $maxPage = ceil($this->totalHistory / $this->historyPerPage);
        if ($page >= 1 && $page <= $maxPage) {
            $this->historyPage = $page;
            $this->loadRiwayat();
        }
    }

    public function applyTemplate($templateId)
    {
        $template = SoapieTemplate::find($templateId);

        if ($template) {
            $this->keluhan = $template->subjective ?: $this->keluhan;
            $this->pemeriksaan = $template->objective ?: $this->pemeriksaan;
            $this->penilaian = $template->assessment ?: $this->penilaian;
            $this->rtl = $template->plan ?: $this->rtl;
            $this->instruksi = $template->intervention ?: $this->instruksi;
            $this->evaluasi = $template->evaluation ?: $this->evaluasi;

            $this->closeTemplateModal();

            Notification::make()
                ->title('Template "' . $template->nama_template . '" berhasil diterapkan')
                ->success()
                ->send();
        }
    }

    public function showCreateTemplateForm()
    {
        $this->showCreateTemplate = true;
        $this->newTemplateName = '';
        $this->newTemplateCategory = '';
        $this->newTemplateDescription = '';
        $this->newTemplateIsPublic = $this->isAdmin; // Only admin can create public templates by default

        // Pre-fill with current SOAPIE if exists
        $this->newTemplateSubjective = $this->keluhan;
        $this->newTemplateObjective = $this->pemeriksaan;
        $this->newTemplateAssessment = $this->penilaian;
        $this->newTemplatePlan = $this->rtl;
        $this->newTemplateIntervention = $this->instruksi;
        $this->newTemplateEvaluation = $this->evaluasi;
    }

    public function hideCreateTemplateForm()
    {
        $this->showCreateTemplate = false;
    }

    public function saveNewTemplate()
    {
        // Check permission to create templates
        if (!auth()->user()->hasPermissionTo('create_soapie_templates')) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda tidak memiliki izin untuk membuat template SOAPIE')
                ->danger()
                ->send();
            return;
        }

        $this->validate([
            'newTemplateName' => 'required|min:3|max:255',
            'newTemplateCategory' => 'nullable|max:100',
            'newTemplateDescription' => 'nullable|max:500'
        ]);

        // Validasi minimal SOAPIE harus terisi
        if (empty($this->newTemplateSubjective) && empty($this->newTemplateObjective) && empty($this->newTemplatePlan)) {
            Notification::make()
                ->title('Error: Template kosong')
                ->body('Minimal isi Subjective (S), Objective (O), atau Plan (P) sebelum menyimpan template')
                ->danger()
                ->send();
            return;
        }

        $currentNip = $this->nip ?: (auth()->user()->pegawai->nik ?? '-');

        // Check permission for public templates
        $canCreatePublic = auth()->user()->hasPermissionTo('create_public_soapie_templates');
        $isPublic = $this->newTemplateIsPublic && $canCreatePublic;

        SoapieTemplate::create([
            'nama_template' => $this->newTemplateName,
            'subjective' => $this->newTemplateSubjective ?: null,
            'objective' => $this->newTemplateObjective ?: null,
            'assessment' => $this->newTemplateAssessment ?: null,
            'plan' => $this->newTemplatePlan ?: null,
            'intervention' => $this->newTemplateIntervention ?: null,
            'evaluation' => $this->newTemplateEvaluation ?: null,
            'nip' => $currentNip,
            'is_public' => $isPublic,
            'kategori' => $this->newTemplateCategory ?: null,
            'keterangan' => $this->newTemplateDescription ?: null
        ]);

        $this->hideCreateTemplateForm();
        $this->loadTemplates();

        Notification::make()
            ->title('Template "' . $this->newTemplateName . '" berhasil disimpan')
            ->body('Template berhasil dibuat dengan data SOAPIE yang sedang diisi')
            ->success()
            ->send();
    }

    public function saveCurrentAsTemplate()
    {
        // Check permission to create templates
        if (!auth()->user()->hasPermissionTo('create_soapie_templates')) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda tidak memiliki izin untuk membuat template SOAPIE')
                ->danger()
                ->send();
            return;
        }

        $currentNip = $this->nip ?: (auth()->user()->pegawai->nik ?? '-');

        $templateName = 'Auto Template - ' . date('Y-m-d H:i:s');

        SoapieTemplate::create([
            'nama_template' => $templateName,
            'subjective' => $this->keluhan,
            'objective' => $this->pemeriksaan,
            'assessment' => $this->penilaian,
            'plan' => $this->rtl,
            'intervention' => $this->instruksi,
            'evaluation' => $this->evaluasi,
            'nip' => $currentNip,
            'is_public' => false,
            'kategori' => 'Auto Generated',
            'keterangan' => 'Template otomatis dari pemeriksaan'
        ]);

        Notification::make()
            ->title('SOAPIE berhasil disimpan sebagai template')
            ->success()
            ->send();
    }

    public function deleteTemplate($templateId)
    {
        // Check permission to delete templates
        if (!auth()->user()->hasPermissionTo('delete_soapie_templates')) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda tidak memiliki izin untuk menghapus template SOAPIE')
                ->danger()
                ->send();
            return;
        }

        $template = SoapieTemplate::find($templateId);

        if ($template) {
            $currentNip = $this->nip ?: (auth()->user()->username ?? '-');
            $canEditAll = auth()->user()->hasPermissionTo('edit_all_soapie_templates');

            // Check if user can delete (owner or has permission for all templates)
            if ($template->nip === $currentNip || $canEditAll) {
                $templateName = $template->nama_template;
                $template->delete();

                $this->loadTemplates();

                Notification::make()
                    ->title('Template "' . $templateName . '" berhasil dihapus')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Akses Ditolak')
                    ->body('Anda hanya bisa menghapus template yang Anda buat sendiri')
                    ->danger()
                    ->send();
            }
        }
    }

    public function fillTTVFromPrevious()
    {
        // Check permission to fill TTV from previous examinations
        if (!auth()->user()->hasPermissionTo('fill_ttv_from_previous')) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda tidak memiliki izin untuk mengisi TTV dari pemeriksaan sebelumnya')
                ->danger()
                ->send();
            return;
        }

        if (empty($this->noRkmMedis)) {
            Notification::make()
                ->title('Error: No patient data found')
                ->warning()
                ->send();
            return;
        }

        // Get all no_rawat for this patient
        $noRawatList = \DB::table('reg_periksa')
            ->where('no_rkm_medis', $this->noRkmMedis)
            ->pluck('no_rawat')
            ->toArray();

        if (empty($noRawatList)) {
            Notification::make()
                ->title('Tidak ada data TTV sebelumnya')
                ->body('Belum ada pemeriksaan sebelumnya untuk pasien ini')
                ->warning()
                ->send();
            return;
        }

        // Get the latest examination data for this patient from all visits (excluding current form if editing)
        $query = PemeriksaanRalan::whereIn('no_rawat', $noRawatList)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc');

        // If editing, exclude current record
        if ($this->editingId) {
            $query = $query->where(function($q) {
                $q->where('tgl_perawatan', '!=', $this->tgl_perawatan)
                  ->orWhere('jam_rawat', '!=', $this->jam_rawat);
            });
        }

        $latestExam = $query->first();

        if ($latestExam) {
            // Fill TTV fields from previous examination
            $this->suhu_tubuh = $latestExam->suhu_tubuh ?: $this->suhu_tubuh;
            $this->tensi = $latestExam->tensi ?: $this->tensi;
            $this->nadi = $latestExam->nadi ?: $this->nadi;
            $this->respirasi = $latestExam->respirasi ?: $this->respirasi;
            $this->spo2 = $latestExam->spo2 ?: $this->spo2;
            $this->tinggi = $latestExam->tinggi ?: $this->tinggi;
            $this->berat = $latestExam->berat ?: $this->berat;
            $this->gcs = $latestExam->gcs ?: $this->gcs;
            $this->kesadaran = $latestExam->kesadaran ?: $this->kesadaran;
            $this->alergi = $latestExam->alergi ?: $this->alergi;
            $this->lingkar_perut = $latestExam->lingkar_perut ?: $this->lingkar_perut;

            $examDate = \Carbon\Carbon::parse($latestExam->tgl_perawatan)->format('d/m/Y');
            $examTime = substr($latestExam->jam_rawat, 0, 5);

            Notification::make()
                ->title('TTV berhasil diisi dari pemeriksaan sebelumnya')
                ->body("Data diambil dari pemeriksaan tanggal {$examDate} jam {$examTime}")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Tidak ada data TTV sebelumnya')
                ->body('Belum ada pemeriksaan sebelumnya untuk pasien ini')
                ->warning()
                ->send();
        }
    }

    public function testMethod()
    {
        \Log::info('testMethod called successfully!');
        session()->flash('message', 'Test method berhasil dipanggil!');
    }
    
    // Permission helper methods for Blade template
    public function canViewTemplates(): bool
    {
        return auth()->user()->hasPermissionTo('view_soapie_templates');
    }

    public function canCreateTemplates(): bool
    {
        return auth()->user()->hasPermissionTo('create_soapie_templates');
    }

    public function canCreatePublicTemplates(): bool
    {
        return auth()->user()->hasPermissionTo('create_public_soapie_templates');
    }

    public function canDeleteTemplates(): bool
    {
        return auth()->user()->hasPermissionTo('delete_soapie_templates');
    }

    public function canEditAllTemplates(): bool
    {
        return auth()->user()->hasPermissionTo('edit_all_soapie_templates');
    }

    public function canEditOwnTemplates(): bool
    {
        return auth()->user()->hasPermissionTo('edit_own_soapie_templates');
    }

    public function canFillTtvFromPrevious(): bool
    {
        return auth()->user()->hasPermissionTo('fill_ttv_from_previous');
    }

    public function canManageAllExaminations(): bool
    {
        return auth()->user()->hasPermissionTo('manage_all_examinations');
    }

    public function render()
    {
        return view('livewire.pemeriksaan-ralan-form');
    }
}