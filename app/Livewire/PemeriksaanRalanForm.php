<?php

namespace App\Livewire;

use App\Models\PemeriksaanRalan;
use App\Models\Pegawai;
use App\Models\SoapieTemplate;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Validate;

class PemeriksaanRalanForm extends Component
{
    public string $noRawat;
    
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

    // History data
    public $riwayatPemeriksaan = [];
    
    // Edit mode
    public $editingId = null;
    
    // Display limit
    public $perPage = 2;
    
    // Pegawai list
    public $pegawaiList = [];
    public $isAdmin = false;

    // Template functionality
    public $soapieTemplates = [];
    public $showTemplateModal = false;
    public $selectedTemplate = null;
    public $saveToTemplate = false;
    public $showCreateTemplate = false;

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
        
        // Check if user is admin
        $this->isAdmin = auth()->user()->hasRole('super_admin') || auth()->user()->hasPermissionTo('manage_all_examinations');
        
        // Load pegawai list for admin
        if ($this->isAdmin) {
            $this->pegawaiList = Pegawai::select('nik', 'nama')
                ->orderBy('nama')
                ->get()
                ->pluck('nama', 'nik')
                ->toArray();
        }
        
        $this->resetForm();
        $this->loadRiwayat();
        $this->loadTemplates();
    }
    
    public function simpanPemeriksaan()
    {
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
            // Update existing record
            try {
                PemeriksaanRalan::where('no_rawat', $this->noRawat)
                    ->where('tgl_perawatan', $this->tgl_perawatan)
                    ->where('jam_rawat', $this->jam_rawat)
                    ->update($data);
                $message = 'Pemeriksaan SOAP berhasil diupdate';
            } catch (\Exception $e) {
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
        $this->refreshDateTime();
        $this->suhu_tubuh = '';
        $this->tensi = '';
        $this->nadi = '';
        $this->respirasi = '';
        $this->spo2 = '';
        $this->tinggi = '';
        $this->berat = '';
        $this->gcs = '';
        $this->kesadaran = '';
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

        // Set NIP based on role
        if ($this->isAdmin) {
            $this->nip = ''; // Admin can choose
        } else {
            $this->nip = auth()->user()->pegawai->nik ?? auth()->user()->username ?? '-';
        }
    }
    
    public function editPemeriksaan($tglPerawatan, $jamRawat)
    {
        try {
            \Log::info('EditPemeriksaan called', [
                'noRawat' => $this->noRawat,
                'tglPerawatan' => $tglPerawatan,
                'jamRawat' => $jamRawat
            ]);
            
            $pemeriksaan = PemeriksaanRalan::where('no_rawat', $this->noRawat)
                ->where('tgl_perawatan', $tglPerawatan)
                ->where('jam_rawat', $jamRawat)
                ->first();
                
            \Log::info('Pemeriksaan found', ['found' => $pemeriksaan ? 'yes' : 'no']);
                
            if ($pemeriksaan) {
                $rawAttrs = $pemeriksaan->getAttributes();
                $this->editingId = $rawAttrs['tgl_perawatan'] . '-' . $rawAttrs['jam_rawat'];
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
    
    public function loadRiwayat(): void
    {
        $totalQuery = PemeriksaanRalan::where('no_rawat', $this->noRawat);
        $this->totalRecords = $totalQuery->count();
        
        $data = PemeriksaanRalan::where('no_rawat', $this->noRawat)
            ->with(['petugas:nik,nama'])
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->limit($this->perPage)
            ->get();
            
        $this->riwayatPemeriksaan = $data->map(function($item) {
            $array = $item->toArray();
            // Keep raw values for edit function - use raw database values
            $rawAttrs = $item->getAttributes();
            $array['tgl_perawatan_raw'] = $rawAttrs['tgl_perawatan'];
            $array['jam_rawat_raw'] = $rawAttrs['jam_rawat'];
            return $array;
        })->toArray();
    }
    
    public $totalRecords = 0;
    
    public function loadTemplates(): void
    {
        $currentNip = $this->nip ?: (auth()->user()->pegawai->nik ?? auth()->user()->username ?? '-');

        $this->soapieTemplates = SoapieTemplate::forUser($currentNip)
            ->orderBy('is_public', 'asc')
            ->orderBy('nama_template', 'asc')
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

        $currentNip = $this->nip ?: (auth()->user()->pegawai->nik ?? auth()->user()->username ?? '-');

        SoapieTemplate::create([
            'nama_template' => $this->newTemplateName,
            'subjective' => $this->newTemplateSubjective ?: null,
            'objective' => $this->newTemplateObjective ?: null,
            'assessment' => $this->newTemplateAssessment ?: null,
            'plan' => $this->newTemplatePlan ?: null,
            'intervention' => $this->newTemplateIntervention ?: null,
            'evaluation' => $this->newTemplateEvaluation ?: null,
            'nip' => $currentNip,
            'is_public' => $this->newTemplateIsPublic && $this->isAdmin, // Only admin can create public templates
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
        $currentNip = $this->nip ?: (auth()->user()->pegawai->nik ?? auth()->user()->username ?? '-');

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
        $template = SoapieTemplate::find($templateId);

        if ($template) {
            $currentNip = $this->nip ?: (auth()->user()->pegawai->nik ?? auth()->user()->username ?? '-');

            // Check if user can delete (owner or admin)
            if ($template->nip === $currentNip || $this->isAdmin) {
                $templateName = $template->nama_template;
                $template->delete();

                $this->loadTemplates();

                Notification::make()
                    ->title('Template "' . $templateName . '" berhasil dihapus')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Error: Tidak bisa menghapus template')
                    ->body('Anda hanya bisa menghapus template yang Anda buat sendiri')
                    ->danger()
                    ->send();
            }
        }
    }

    public function testMethod()
    {
        \Log::info('testMethod called successfully!');
        session()->flash('message', 'Test method berhasil dipanggil!');
    }
    
    public function render()
    {
        return view('livewire.pemeriksaan-ralan-form');
    }
}