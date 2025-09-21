<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Databarang extends Model
{
    protected $table = 'databarang';
    protected $primaryKey = 'kode_brng';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'nama_brng',
        'kode_satbesar',
        'kode_sat',
        'letak_barang',
        'dasar',
        'h_beli',
        'ralan',
        'kelas1',
        'kelas2',
        'kelas3',
        'utama',
        'vip',
        'vvip',
        'beliluar',
        'jualbebas',
        'karyawan',
        'stokminimal',
        'kdjns',
        'isi',
        'kapasitas',
        'expire',
        'status',
        'kode_industri',
        'kode_kategori',
        'kode_golongan'
    ];

    protected $casts = [
        'dasar' => 'double',
        'h_beli' => 'double',
        'ralan' => 'double',
        'kelas1' => 'double',
        'kelas2' => 'double',
        'kelas3' => 'double',
        'utama' => 'double',
        'vip' => 'double',
        'vvip' => 'double',
        'beliluar' => 'double',
        'jualbebas' => 'double',
        'karyawan' => 'double',
        'stokminimal' => 'double',
        'isi' => 'double',
        'kapasitas' => 'double',
        'expire' => 'date',
    ];

    // Relationships
    public function resepDokter(): HasMany
    {
        return $this->hasMany(ResepDokter::class, 'kode_brng', 'kode_brng');
    }

    public function satuanBesar()
    {
        return $this->belongsTo(Kodesatuan::class, 'kode_satbesar', 'kode_sat');
    }

    public function satuanKecil()
    {
        return $this->belongsTo(Kodesatuan::class, 'kode_sat', 'kode_sat');
    }

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class, 'kdjns', 'kdjns');
    }

    public function industriFarmasi()
    {
        return $this->belongsTo(IndustriFarmasi::class, 'kode_industri', 'kode_industri');
    }

    public function kategoriBarang()
    {
        return $this->belongsTo(KategoriBarang::class, 'kode_kategori', 'kode');
    }

    public function gudangBarang(): HasMany
    {
        return $this->hasMany(GudangBarang::class, 'kode_brng', 'kode_brng');
    }

    // Scopes
    public function scopeObat($query)
    {
        // Return all items for now, can be filtered later by category
        // In real implementation, you might have a category field to filter medicines
        return $query;
    }

    public function scopeSearchByName($query, $search)
    {
        return $query->where('nama_brng', 'like', '%' . $search . '%');
    }

    public function scopeSearchByCode($query, $search)
    {
        return $query->where('kode_brng', 'like', '%' . $search . '%');
    }

    // Helper methods
    public function getFormattedHargaRalanAttribute(): string
    {
        return 'Rp ' . number_format($this->ralan, 0, ',', '.');
    }

    public function getFormattedHargaBeliAttribute(): string
    {
        return 'Rp ' . number_format($this->h_beli, 0, ',', '.');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->kode_brng . ' - ' . $this->nama_brng;
    }

    public function getTotalStokAttribute(): float
    {
        return $this->gudangBarang()->sum('stok');
    }

    public function getFormattedTotalStokAttribute(): string
    {
        return number_format($this->total_stok, 0);
    }

    public function getKomposisiAttribute(): string
    {
        // Assuming composition might be in a separate field or calculated
        // For now, return isi and kapasitas info
        $komposisi = '';
        if ($this->isi > 0) {
            $komposisi .= 'Isi: ' . number_format($this->isi, 0);
        }
        if ($this->kapasitas > 0) {
            $komposisi .= ($komposisi ? ' | ' : '') . 'Kapasitas: ' . number_format($this->kapasitas, 0);
        }
        return $komposisi ?: '-';
    }
}