<?php

namespace App\Livewire;

use App\Models\ResumePasien;
use App\Models\RegPeriksa;
use App\Models\PenyakitIcd10;
use App\Models\Icd9;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Attributes\Validate;

class ResumePasienForm extends Component
{
    public $noRawat;

    #[Validate('required')]
    public $keluhan_utama = '';

    #[Validate('required')]
    public $jalannya_penyakit = '';

    public $pemeriksaan_penunjang = '';
    public $hasil_laborat = '';

    #[Validate('required')]
    public $kd_diagnosa_utama = '';
    public $diagnosa_utama = '';

    public $kd_diagnosa_sekunder = '';
    public $diagnosa_sekunder = '';

    public $kd_diagnosa_sekunder2 = '';
    public $diagnosa_sekunder2 = '';

    public $kd_diagnosa_sekunder3 = '';
    public $diagnosa_sekunder3 = '';

    public $kd_diagnosa_sekunder4 = '';
    public $diagnosa_sekunder4 = '';

    public $kd_prosedur_utama = '';
    public $prosedur_utama = '';

    public $kd_prosedur_sekunder = '';
    public $prosedur_sekunder = '';

    public $kd_prosedur_sekunder2 = '';
    public $prosedur_sekunder2 = '';

    public $kd_prosedur_sekunder3 = '';
    public $prosedur_sekunder3 = '';

    #[Validate('required')]
    public $kondisi_pulang = 'Hidup';

    public $obat_pulang = '';

    // Properties untuk modal keluhan utama
    public $selectedSubjek = [];
    public $selectedObjek = [];

    // Properties untuk modal anamnesis
    public $selectedAssessment = [];
    public $selectedPlanning = [];

    public function mount()
    {
        $this->loadResumeData();
    }

    private function loadResumeData()
    {
        $resume = ResumePasien::where('no_rawat', $this->noRawat)->first();

        if ($resume) {
            // Jika resume sudah ada, gunakan data resume
            $this->keluhan_utama = $resume->keluhan_utama;
            $this->jalannya_penyakit = $resume->jalannya_penyakit;
            $this->pemeriksaan_penunjang = $resume->pemeriksaan_penunjang;
            $this->hasil_laborat = $resume->hasil_laborat;

            $this->kd_diagnosa_utama = $resume->kd_diagnosa_utama;
            $this->diagnosa_utama = $resume->diagnosa_utama;

            $this->kd_diagnosa_sekunder = $resume->kd_diagnosa_sekunder;
            $this->diagnosa_sekunder = $resume->diagnosa_sekunder;

            $this->kd_diagnosa_sekunder2 = $resume->kd_diagnosa_sekunder2;
            $this->diagnosa_sekunder2 = $resume->diagnosa_sekunder2;

            $this->kd_diagnosa_sekunder3 = $resume->kd_diagnosa_sekunder3;
            $this->diagnosa_sekunder3 = $resume->diagnosa_sekunder3;

            $this->kd_diagnosa_sekunder4 = $resume->kd_diagnosa_sekunder4;
            $this->diagnosa_sekunder4 = $resume->diagnosa_sekunder4;

            $this->kd_prosedur_utama = $resume->kd_prosedur_utama;
            $this->prosedur_utama = $resume->prosedur_utama;

            $this->kd_prosedur_sekunder = $resume->kd_prosedur_sekunder;
            $this->prosedur_sekunder = $resume->prosedur_sekunder;

            $this->kd_prosedur_sekunder2 = $resume->kd_prosedur_sekunder2;
            $this->prosedur_sekunder2 = $resume->prosedur_sekunder2;

            $this->kd_prosedur_sekunder3 = $resume->kd_prosedur_sekunder3;
            $this->prosedur_sekunder3 = $resume->prosedur_sekunder3;

            $this->kondisi_pulang = $resume->kondisi_pulang;
            $this->obat_pulang = $resume->obat_pulang;
        } else {
            // Jika resume belum ada, auto-fill dari data yang sudah ada
            $this->autoFillFromExistingData();
        }
    }

    private function autoFillFromExistingData()
    {
        // 1. Auto-fill dari SOAPIE (Pemeriksaan Ralan)
        $this->autoFillFromSoapie();

        // 2. Auto-fill dari Diagnosa
        $this->autoFillFromDiagnosa();

        // 3. Auto-fill dari Prosedur
        $this->autoFillFromProsedur();

        // 4. Auto-fill dari Resep Obat
        $this->autoFillFromResepObat();
    }

    private function autoFillFromSoapie()
    {
        $pemeriksaan = \DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $this->noRawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        if ($pemeriksaan) {
            // Keluhan utama dari kolom keluhan
            if (!empty($pemeriksaan->keluhan) && empty($this->keluhan_utama)) {
                $this->keluhan_utama = $pemeriksaan->keluhan;
            }

            // Jalannya penyakit dari pemeriksaan + penilaian + rtl
            $jalannyaPenyakit = [];
            if (!empty($pemeriksaan->pemeriksaan)) {
                $jalannyaPenyakit[] = "Pemeriksaan: " . $pemeriksaan->pemeriksaan;
            }
            if (!empty($pemeriksaan->penilaian)) {
                $jalannyaPenyakit[] = "Penilaian: " . $pemeriksaan->penilaian;
            }
            if (!empty($pemeriksaan->rtl)) {
                $jalannyaPenyakit[] = "Rencana Tindak Lanjut: " . $pemeriksaan->rtl;
            }
            if (!empty($jalannyaPenyakit) && empty($this->jalannya_penyakit)) {
                $this->jalannya_penyakit = implode("\n\n", $jalannyaPenyakit);
            }

            // Pemeriksaan penunjang dari assessment
            if (!empty($pemeriksaan->asesmen) && empty($this->pemeriksaan_penunjang)) {
                $this->pemeriksaan_penunjang = $pemeriksaan->asesmen;
            }
        }
    }

    private function autoFillFromDiagnosa()
    {
        $diagnosas = \DB::table('diagnosa_pasien')
            ->leftJoin('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->where('diagnosa_pasien.no_rawat', $this->noRawat)
            ->orderBy('diagnosa_pasien.prioritas', 'asc')
            ->select('diagnosa_pasien.*', 'penyakit.nm_penyakit')
            ->get();

        $index = 0;
        foreach ($diagnosas as $diagnosa) {
            if ($index == 0 && empty($this->kd_diagnosa_utama)) {
                // Diagnosa Utama
                $this->kd_diagnosa_utama = $diagnosa->kd_penyakit;
                $this->diagnosa_utama = $diagnosa->nm_penyakit;
            } elseif ($index == 1 && empty($this->kd_diagnosa_sekunder)) {
                // Diagnosa Sekunder 1
                $this->kd_diagnosa_sekunder = $diagnosa->kd_penyakit;
                $this->diagnosa_sekunder = $diagnosa->nm_penyakit;
            } elseif ($index == 2 && empty($this->kd_diagnosa_sekunder2)) {
                // Diagnosa Sekunder 2
                $this->kd_diagnosa_sekunder2 = $diagnosa->kd_penyakit;
                $this->diagnosa_sekunder2 = $diagnosa->nm_penyakit;
            } elseif ($index == 3 && empty($this->kd_diagnosa_sekunder3)) {
                // Diagnosa Sekunder 3
                $this->kd_diagnosa_sekunder3 = $diagnosa->kd_penyakit;
                $this->diagnosa_sekunder3 = $diagnosa->nm_penyakit;
            } elseif ($index == 4 && empty($this->kd_diagnosa_sekunder4)) {
                // Diagnosa Sekunder 4
                $this->kd_diagnosa_sekunder4 = $diagnosa->kd_penyakit;
                $this->diagnosa_sekunder4 = $diagnosa->nm_penyakit;
            }
            $index++;
            if ($index >= 5) break; // Maksimal 5 diagnosa (1 utama + 4 sekunder)
        }
    }

    private function autoFillFromProsedur()
    {
        $prosedurs = \DB::table('prosedur_pasien')
            ->leftJoin('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
            ->where('prosedur_pasien.no_rawat', $this->noRawat)
            ->orderBy('prosedur_pasien.prioritas', 'asc')
            ->select('prosedur_pasien.*', 'icd9.deskripsi_panjang')
            ->get();

        $index = 0;
        foreach ($prosedurs as $prosedur) {
            if ($index == 0 && empty($this->kd_prosedur_utama)) {
                // Prosedur Utama
                $this->kd_prosedur_utama = $prosedur->kode;
                $this->prosedur_utama = $prosedur->deskripsi_panjang;
            } elseif ($index == 1 && empty($this->kd_prosedur_sekunder)) {
                // Prosedur Sekunder 1
                $this->kd_prosedur_sekunder = $prosedur->kode;
                $this->prosedur_sekunder = $prosedur->deskripsi_panjang;
            } elseif ($index == 2 && empty($this->kd_prosedur_sekunder2)) {
                // Prosedur Sekunder 2
                $this->kd_prosedur_sekunder2 = $prosedur->kode;
                $this->prosedur_sekunder2 = $prosedur->deskripsi_panjang;
            } elseif ($index == 3 && empty($this->kd_prosedur_sekunder3)) {
                // Prosedur Sekunder 3
                $this->kd_prosedur_sekunder3 = $prosedur->kode;
                $this->prosedur_sekunder3 = $prosedur->deskripsi_panjang;
            }
            $index++;
            if ($index >= 4) break; // Maksimal 4 prosedur (1 utama + 3 sekunder)
        }
    }

    private function autoFillFromResepObat()
    {
        // Ambil resep obat terbaru
        $resepObats = \DB::table('resep_obat')
            ->leftJoin('resep_dokter', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
            ->leftJoin('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
            ->where('resep_obat.no_rawat', $this->noRawat)
            ->orderBy('resep_obat.tgl_peresepan', 'desc')
            ->orderBy('resep_obat.jam_peresepan', 'desc')
            ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
            ->get();

        if ($resepObats->isNotEmpty() && empty($this->obat_pulang)) {
            $obatPulang = [];

            foreach ($resepObats as $resep) {
                if (!empty($resep->nama_brng)) {
                    $obatLine = $resep->nama_brng;
                    if (!empty($resep->jml)) {
                        $obatLine .= " " . $resep->jml;
                    }
                    if (!empty($resep->aturan_pakai)) {
                        $obatLine .= " - " . $resep->aturan_pakai;
                    }
                    $obatPulang[] = $obatLine;
                }
            }

            if (!empty($obatPulang)) {
                $this->obat_pulang = implode("\n", $obatPulang);

                // Tambahkan anjuran umum
                $this->obat_pulang .= "\n\nAnjuran:\n- Minum obat sesuai aturan\n- Kontrol kembali jika keluhan tidak membaik\n- Istirahat cukup dan makan makanan bergizi";
            }
        }
    }

    public function updatedKdDiagnosaUtama()
    {
        if ($this->kd_diagnosa_utama) {
            $penyakit = PenyakitIcd10::find($this->kd_diagnosa_utama);
            $this->diagnosa_utama = $penyakit?->nm_penyakit ?? '';
        }
    }

    public function updatedKdDiagnosaSekunder()
    {
        if ($this->kd_diagnosa_sekunder) {
            $penyakit = PenyakitIcd10::find($this->kd_diagnosa_sekunder);
            $this->diagnosa_sekunder = $penyakit?->nm_penyakit ?? '';
        }
    }

    public function updatedKdDiagnosaSekunder2()
    {
        if ($this->kd_diagnosa_sekunder2) {
            $penyakit = PenyakitIcd10::find($this->kd_diagnosa_sekunder2);
            $this->diagnosa_sekunder2 = $penyakit?->nm_penyakit ?? '';
        }
    }

    public function updatedKdDiagnosaSekunder3()
    {
        if ($this->kd_diagnosa_sekunder3) {
            $penyakit = PenyakitIcd10::find($this->kd_diagnosa_sekunder3);
            $this->diagnosa_sekunder3 = $penyakit?->nm_penyakit ?? '';
        }
    }

    public function updatedKdDiagnosaSekunder4()
    {
        if ($this->kd_diagnosa_sekunder4) {
            $penyakit = PenyakitIcd10::find($this->kd_diagnosa_sekunder4);
            $this->diagnosa_sekunder4 = $penyakit?->nm_penyakit ?? '';
        }
    }

    public function updatedKdProsedurUtama()
    {
        if ($this->kd_prosedur_utama) {
            $prosedur = Icd9::find($this->kd_prosedur_utama);
            $this->prosedur_utama = $prosedur?->deskripsi_panjang ?? '';
        }
    }

    public function updatedKdProsedurSekunder()
    {
        if ($this->kd_prosedur_sekunder) {
            $prosedur = Icd9::find($this->kd_prosedur_sekunder);
            $this->prosedur_sekunder = $prosedur?->deskripsi_panjang ?? '';
        }
    }

    public function updatedKdProsedurSekunder2()
    {
        if ($this->kd_prosedur_sekunder2) {
            $prosedur = Icd9::find($this->kd_prosedur_sekunder2);
            $this->prosedur_sekunder2 = $prosedur?->deskripsi_panjang ?? '';
        }
    }

    public function updatedKdProsedurSekunder3()
    {
        if ($this->kd_prosedur_sekunder3) {
            $prosedur = Icd9::find($this->kd_prosedur_sekunder3);
            $this->prosedur_sekunder3 = $prosedur?->deskripsi_panjang ?? '';
        }
    }

    public function getPenyakitOptions($search = '')
    {
        if (strlen($search) < 2) {
            return [];
        }

        return PenyakitIcd10::where('nm_penyakit', 'like', "%{$search}%")
            ->orWhere('kd_penyakit', 'like', "%{$search}%")
            ->limit(50)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->kd_penyakit => "{$item->kd_penyakit} - {$item->nm_penyakit}"];
            })
            ->toArray();
    }

    public function getProsedurOptions($search = '')
    {
        if (strlen($search) < 2) {
            return [];
        }

        return Icd9::where('deskripsi_panjang', 'like', "%{$search}%")
            ->orWhere('kode', 'like', "%{$search}%")
            ->limit(50)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->kode => "{$item->kode} - {$item->deskripsi_panjang}"];
            })
            ->toArray();
    }

    // Method untuk mendapatkan data SOAPIE
    public function getSoapieOptions()
    {
        return \DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $this->noRawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
    }

    // Method untuk mendapatkan data keluhan utama (subjek dan objek)
    public function getKeluhanUtamaOptions()
    {
        return \DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $this->noRawat)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNotNull('keluhan')->where('keluhan', '!=', '');
                })->orWhere(function($q) {
                    $q->whereNotNull('pemeriksaan')->where('pemeriksaan', '!=', '');
                });
            })
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
    }

    // Toggle subjek selection
    public function toggleSubjek($id, $keluhan)
    {
        $key = $id;
        if (isset($this->selectedSubjek[$key])) {
            unset($this->selectedSubjek[$key]);
        } else {
            $this->selectedSubjek[$key] = $keluhan;
        }
    }

    // Toggle objek selection
    public function toggleObjek($id, $pemeriksaan)
    {
        $key = $id;
        if (isset($this->selectedObjek[$key])) {
            unset($this->selectedObjek[$key]);
        } else {
            $this->selectedObjek[$key] = $pemeriksaan;
        }
    }

    // Apply selected keluhan utama
    public function applyKeluhanUtama()
    {
        $content = [];

        // Tambahkan subjek tanpa kata "Subjektif:"
        if (!empty($this->selectedSubjek)) {
            foreach ($this->selectedSubjek as $subjek) {
                $content[] = $subjek;
            }
        }

        // Tambahkan objek tanpa kata "Objektif:"
        if (!empty($this->selectedObjek)) {
            foreach ($this->selectedObjek as $objek) {
                $content[] = $objek;
            }
        }

        if (!empty($content)) {
            // Gabung dengan spasi (ke samping), bukan newline (ke bawah)
            $combinedContent = implode(" ", $content);

            // Append ke keluhan_utama (tidak replace)
            if (!empty($this->keluhan_utama)) {
                $this->keluhan_utama .= " " . $combinedContent;
            } else {
                $this->keluhan_utama = $combinedContent;
            }
        }

        // Reset selections
        $this->selectedSubjek = [];
        $this->selectedObjek = [];
    }

    // Method untuk mendapatkan data anamnesis (assessment dan planning)
    public function getAnamnesisOptions()
    {
        return \DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $this->noRawat)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNotNull('penilaian')->where('penilaian', '!=', '');
                })->orWhere(function($q) {
                    $q->whereNotNull('rtl')->where('rtl', '!=', '');
                });
            })
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->get();
    }

    // Toggle assessment selection
    public function toggleAssessment($id, $penilaian)
    {
        $key = $id;
        if (isset($this->selectedAssessment[$key])) {
            unset($this->selectedAssessment[$key]);
        } else {
            $this->selectedAssessment[$key] = $penilaian;
        }
    }

    // Toggle planning selection
    public function togglePlanning($id, $rtl)
    {
        $key = $id;
        if (isset($this->selectedPlanning[$key])) {
            unset($this->selectedPlanning[$key]);
        } else {
            $this->selectedPlanning[$key] = $rtl;
        }
    }

    // Apply selected anamnesis
    public function applyAnamnesis()
    {
        $content = [];

        // Tambahkan assessment tanpa kata "Assessment:"
        if (!empty($this->selectedAssessment)) {
            foreach ($this->selectedAssessment as $assessment) {
                $content[] = $assessment;
            }
        }

        // Tambahkan planning tanpa kata "Planning:"
        if (!empty($this->selectedPlanning)) {
            foreach ($this->selectedPlanning as $planning) {
                $content[] = $planning;
            }
        }

        if (!empty($content)) {
            // Gabung dengan spasi (ke samping), bukan newline (ke bawah)
            $combinedContent = implode(" ", $content);

            // Append ke jalannya_penyakit (tidak replace)
            if (!empty($this->jalannya_penyakit)) {
                $this->jalannya_penyakit .= " " . $combinedContent;
            } else {
                $this->jalannya_penyakit = $combinedContent;
            }
        }

        // Reset selections
        $this->selectedAssessment = [];
        $this->selectedPlanning = [];
    }

    // Method untuk mendapatkan diagnosa yang sudah ada
    public function getDiagnosaOptions()
    {
        return \DB::table('diagnosa_pasien')
            ->leftJoin('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->where('diagnosa_pasien.no_rawat', $this->noRawat)
            ->orderBy('diagnosa_pasien.prioritas', 'asc')
            ->select('diagnosa_pasien.*', 'penyakit.nm_penyakit')
            ->get();
    }

    // Method untuk mendapatkan prosedur yang sudah ada
    public function getProsedurExistingOptions()
    {
        return \DB::table('prosedur_pasien')
            ->leftJoin('icd9', 'prosedur_pasien.kode', '=', 'icd9.kode')
            ->where('prosedur_pasien.no_rawat', $this->noRawat)
            ->orderBy('prosedur_pasien.prioritas', 'asc')
            ->select('prosedur_pasien.*', 'icd9.deskripsi_panjang')
            ->get();
    }

    // Method untuk mendapatkan hasil laboratorium
    public function getLabOptions()
    {
        return \DB::table('detail_periksa_lab')
            ->leftJoin('template_laboratorium', function($join) {
                $join->on('detail_periksa_lab.kd_jenis_prw', '=', 'template_laboratorium.kd_jenis_prw')
                     ->on('detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template');
            })
            ->where('detail_periksa_lab.no_rawat', $this->noRawat)
            ->orderBy('detail_periksa_lab.tgl_periksa', 'desc')
            ->orderBy('detail_periksa_lab.jam', 'desc')
            ->select('detail_periksa_lab.*', 'template_laboratorium.Pemeriksaan as nama_pemeriksaan')
            ->get();
    }

    // Method untuk mendapatkan hasil radiologi
    public function getRadiologiOptions()
    {
        return \DB::table('permintaan_radiologi')
            ->leftJoin('hasil_radiologi', 'permintaan_radiologi.no_rawat', '=', 'hasil_radiologi.no_rawat')
            ->where('permintaan_radiologi.no_rawat', $this->noRawat)
            ->orderBy('permintaan_radiologi.tgl_permintaan', 'desc')
            ->select('permintaan_radiologi.*', 'hasil_radiologi.hasil')
            ->get();
    }

    // Action methods untuk pilih data
    public function pilihKeluhan($keluhan)
    {
        // Append ke keluhan_utama dengan spasi (ke samping)
        if (!empty($this->keluhan_utama)) {
            $this->keluhan_utama .= " " . $keluhan;
        } else {
            $this->keluhan_utama = $keluhan;
        }
    }

    // Method untuk memilih subjek + objek ke keluhan utama
    public function pilihSubjekObjek($keluhan, $pemeriksaan)
    {
        $content = [];
        if (!empty($keluhan)) {
            $content[] = "Subjektif: " . $keluhan;
        }
        if (!empty($pemeriksaan)) {
            $content[] = "Objektif: " . $pemeriksaan;
        }

        $combinedContent = implode("\n", $content);

        // Append ke keluhan_utama (tidak replace)
        if (!empty($this->keluhan_utama)) {
            $this->keluhan_utama .= "\n\n" . $combinedContent;
        } else {
            $this->keluhan_utama = $combinedContent;
        }
    }

    public function pilihAnamnesis($pemeriksaan, $penilaian, $rtl)
    {
        $anamnesis = [];
        if (!empty($pemeriksaan)) {
            $anamnesis[] = $pemeriksaan;
        }
        if (!empty($penilaian)) {
            $anamnesis[] = $penilaian;
        }
        if (!empty($rtl)) {
            $anamnesis[] = $rtl;
        }

        $combinedAnamnesis = implode(" ", $anamnesis);

        // Append ke jalannya_penyakit dengan spasi (ke samping)
        if (!empty($this->jalannya_penyakit)) {
            $this->jalannya_penyakit .= " " . $combinedAnamnesis;
        } else {
            $this->jalannya_penyakit = $combinedAnamnesis;
        }
    }

    public function pilihPemeriksaanPenunjang($text)
    {
        if (!empty($this->pemeriksaan_penunjang)) {
            $this->pemeriksaan_penunjang .= "\n\n" . $text;
        } else {
            $this->pemeriksaan_penunjang = $text;
        }
    }

    public function pilihHasilLab($text)
    {
        if (!empty($this->hasil_laborat)) {
            $this->hasil_laborat .= "\n\n" . $text;
        } else {
            $this->hasil_laborat = $text;
        }
    }

    public function pilihDiagnosa($kode, $nama, $level = 'utama')
    {
        switch ($level) {
            case 'utama':
                $this->kd_diagnosa_utama = $kode;
                $this->diagnosa_utama = $nama;
                break;
            case 'sekunder':
                $this->kd_diagnosa_sekunder = $kode;
                $this->diagnosa_sekunder = $nama;
                break;
            case 'sekunder2':
                $this->kd_diagnosa_sekunder2 = $kode;
                $this->diagnosa_sekunder2 = $nama;
                break;
            case 'sekunder3':
                $this->kd_diagnosa_sekunder3 = $kode;
                $this->diagnosa_sekunder3 = $nama;
                break;
            case 'sekunder4':
                $this->kd_diagnosa_sekunder4 = $kode;
                $this->diagnosa_sekunder4 = $nama;
                break;
        }
    }

    public function pilihProsedur($kode, $nama, $level = 'utama')
    {
        switch ($level) {
            case 'utama':
                $this->kd_prosedur_utama = $kode;
                $this->prosedur_utama = $nama;
                break;
            case 'sekunder':
                $this->kd_prosedur_sekunder = $kode;
                $this->prosedur_sekunder = $nama;
                break;
            case 'sekunder2':
                $this->kd_prosedur_sekunder2 = $kode;
                $this->prosedur_sekunder2 = $nama;
                break;
            case 'sekunder3':
                $this->kd_prosedur_sekunder3 = $kode;
                $this->prosedur_sekunder3 = $nama;
                break;
        }
    }

    public function simpanResume()
    {
        $this->validate();

        try {
            $data = [
                'no_rawat' => $this->noRawat,
                'kd_dokter' => auth()->user()->username,
                'keluhan_utama' => $this->keluhan_utama,
                'jalannya_penyakit' => $this->jalannya_penyakit,
                'pemeriksaan_penunjang' => $this->pemeriksaan_penunjang,
                'hasil_laborat' => $this->hasil_laborat,
                'diagnosa_utama' => $this->diagnosa_utama,
                'kd_diagnosa_utama' => $this->kd_diagnosa_utama,
                'diagnosa_sekunder' => $this->diagnosa_sekunder,
                'kd_diagnosa_sekunder' => $this->kd_diagnosa_sekunder,
                'diagnosa_sekunder2' => $this->diagnosa_sekunder2,
                'kd_diagnosa_sekunder2' => $this->kd_diagnosa_sekunder2,
                'diagnosa_sekunder3' => $this->diagnosa_sekunder3,
                'kd_diagnosa_sekunder3' => $this->kd_diagnosa_sekunder3,
                'diagnosa_sekunder4' => $this->diagnosa_sekunder4,
                'kd_diagnosa_sekunder4' => $this->kd_diagnosa_sekunder4,
                'prosedur_utama' => $this->prosedur_utama,
                'kd_prosedur_utama' => $this->kd_prosedur_utama,
                'prosedur_sekunder' => $this->prosedur_sekunder,
                'kd_prosedur_sekunder' => $this->kd_prosedur_sekunder,
                'prosedur_sekunder2' => $this->prosedur_sekunder2,
                'kd_prosedur_sekunder2' => $this->kd_prosedur_sekunder2,
                'prosedur_sekunder3' => $this->prosedur_sekunder3,
                'kd_prosedur_sekunder3' => $this->kd_prosedur_sekunder3,
                'kondisi_pulang' => $this->kondisi_pulang,
                'obat_pulang' => $this->obat_pulang,
            ];

            ResumePasien::updateOrCreate(
                ['no_rawat' => $this->noRawat],
                $data
            );

            Notification::make()
                ->title('Berhasil')
                ->body('Resume pasien berhasil disimpan')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Gagal menyimpan resume: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.resume-pasien-form');
    }
}