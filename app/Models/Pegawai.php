<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Pegawai extends Model
{
    use LogsActivity;
    protected $table = 'pegawai';
    
    protected $primaryKey = 'id';
    
    public $timestamps = false;

    protected $attributes = [
        'pengurang' => 0,
        'indek' => 1,
        'cuti_diambil' => 0,
        'dankes' => 0,
        'npwp' => '-',
    ];

    protected $casts = [
        'wajibmasuk' => 'string',
        'tgl_lahir' => 'date',
        'mulai_kerja' => 'date',
        'mulai_kontrak' => 'date',
        'gapok' => 'decimal:2',
        'pengurang' => 'decimal:2',
        'dankes' => 'decimal:2',
        'cuti_diambil' => 'integer',
        'indek' => 'integer',
        'wajibmasuk' => 'integer',
    ];
    
    protected $fillable = [
        'nik',
        'nama',
        'jk',
        'jbtn',
        'jnj_jabatan',
        'kode_kelompok',
        'kode_resiko',
        'kode_emergency',
        'departemen',
        'bidang',
        'stts_wp',
        'stts_kerja',
        'npwp',
        'pendidikan',
        'gapok',
        'tmp_lahir',
        'tgl_lahir',
        'alamat',
        'kota',
        'mulai_kerja',
        'ms_kerja',
        'indexins',
        'bpd',
        'rekening',
        'stts_aktif',
        'wajibmasuk',
        'pengurang',
        'indek',
        'mulai_kontrak',
        'cuti_diambil',
        'dankes',
        'photo',
        'no_ktp',
    ];

    public function jnjJabatanRelation()
    {
        return $this->belongsTo(JnjJabatan::class, 'jnj_jabatan', 'kode');
    }

    public function kelompokJabatanRelation()
    {
        return $this->belongsTo(KelompokJabatan::class, 'kode_kelompok', 'kode_kelompok');
    }

    public function resikoKerjaRelation()
    {
        return $this->belongsTo(ResikoKerja::class, 'kode_resiko', 'kode_resiko');
    }

    public function emergencyIndexRelation()
    {
        return $this->belongsTo(EmergencyIndex::class, 'kode_emergency', 'kode_emergency');
    }

    public function departemenRelation()
    {
        return $this->belongsTo(Departemen::class, 'departemen', 'dep_id');
    }

    public function indexinsDepartemenRelation()
    {
        return $this->belongsTo(Departemen::class, 'indexins', 'dep_id');
    }

    public function bidangRelation()
    {
        return $this->belongsTo(Bidang::class, 'bidang', 'nama');
    }

    public function sttsWpRelation()
    {
        return $this->belongsTo(SttsWp::class, 'stts_wp', 'stts');
    }

    public function sttsKerjaRelation()
    {
        return $this->belongsTo(SttsKerja::class, 'stts_kerja', 'stts');
    }

    public function pendidikanRelation()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan', 'tingkat');
    }

    public function bankRelation()
    {
        return $this->belongsTo(Bank::class, 'bpd', 'namabank');
    }

    public function dokter()
    {
        return $this->hasOne(Dokter::class, 'kd_dokter', 'nik');
    }

    public function petugas()
    {
        return $this->hasOne(Petugas::class, 'nip', 'nik');
    }

    public function berkas_pegawai()
    {
        return $this->hasMany(BerkasPegawai::class, 'no_ktp', 'no_ktp');
    }

    public function getEnumValues($column)
    {
        $instance = new static;
        $type = \DB::select(\DB::raw("SHOW COLUMNS FROM {$instance->getTable()} WHERE Field = '{$column}'"))[0]->Type;
        preg_match_all("/'([^']+)'/", $type, $matches);
        return $matches[1];
    }

    public function getPhotoUrl()
    {
        return route('pegawai.photo', $this->id);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nik', 'nama', 'jk', 'jbtn', 'departemen', 'bidang', 'stts_aktif'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('pegawai');
    }
}