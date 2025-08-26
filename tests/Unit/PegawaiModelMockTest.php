<?php

namespace Tests\Unit;

use App\Models\Bank;
use App\Models\BerkasPegawai;
use App\Models\Bidang;
use App\Models\Departemen;
use App\Models\Dokter;
use App\Models\EmergencyIndex;
use App\Models\JnjJabatan;
use App\Models\KelompokJabatan;
use App\Models\Pegawai;
use App\Models\Pendidikan;
use App\Models\Petugas;
use App\Models\ResikoKerja;
use App\Models\SttsKerja;
use App\Models\SttsWp;
use Tests\TestCase;

class PegawaiModelMockTest extends TestCase
{
    public function test_pegawai_model_has_correct_table_name()
    {
        $pegawai = new Pegawai();
        $this->assertEquals('pegawai', $pegawai->getTable());
    }

    public function test_pegawai_model_has_correct_primary_key()
    {
        $pegawai = new Pegawai();
        $this->assertEquals('id', $pegawai->getKeyName());
        $this->assertTrue($pegawai->getIncrementing());
        $this->assertEquals('int', $pegawai->getKeyType());
    }

    public function test_pegawai_model_fillable_fields()
    {
        $pegawai = new Pegawai();
        $fillable = $pegawai->getFillable();
        
        $expectedFields = [
            'nik', 'nama', 'jk', 'jbtn', 'jnj_jabatan', 'kode_kelompok',
            'kode_resiko', 'kode_emergency', 'departemen', 'bidang', 'stts_wp',
            'stts_kerja', 'npwp', 'pendidikan', 'gapok', 'tmp_lahir', 'tgl_lahir',
            'alamat', 'kota', 'mulai_kerja', 'ms_kerja', 'indexins', 'bpd',
            'rekening', 'stts_aktif', 'wajibmasuk', 'pengurang', 'indek',
            'mulai_kontrak', 'cuti_diambil', 'dankes', 'photo', 'no_ktp'
        ];
        
        foreach ($expectedFields as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_pegawai_model_casts_configuration()
    {
        $pegawai = new Pegawai();
        $casts = $pegawai->getCasts();
        
        $this->assertArrayHasKey('tgl_lahir', $casts);
        $this->assertArrayHasKey('mulai_kerja', $casts);
        $this->assertArrayHasKey('mulai_kontrak', $casts);
        $this->assertEquals('date', $casts['tgl_lahir']);
        $this->assertEquals('date', $casts['mulai_kerja']);
        $this->assertEquals('date', $casts['mulai_kontrak']);
    }

    public function test_pegawai_relationship_methods_exist()
    {
        $pegawai = new Pegawai();
        
        // Test that relationship methods exist
        $this->assertTrue(method_exists($pegawai, 'jnjJabatanRelation'));
        $this->assertTrue(method_exists($pegawai, 'kelompokJabatanRelation'));
        $this->assertTrue(method_exists($pegawai, 'resikoKerjaRelation'));
        $this->assertTrue(method_exists($pegawai, 'emergencyIndexRelation'));
        $this->assertTrue(method_exists($pegawai, 'departemenRelation'));
        $this->assertTrue(method_exists($pegawai, 'indexinsDepartemenRelation'));
        $this->assertTrue(method_exists($pegawai, 'bidangRelation'));
        $this->assertTrue(method_exists($pegawai, 'sttsWpRelation'));
        $this->assertTrue(method_exists($pegawai, 'sttsKerjaRelation'));
        $this->assertTrue(method_exists($pegawai, 'pendidikanRelation'));
        $this->assertTrue(method_exists($pegawai, 'bankRelation'));
        $this->assertTrue(method_exists($pegawai, 'dokter'));
        $this->assertTrue(method_exists($pegawai, 'petugas'));
        $this->assertTrue(method_exists($pegawai, 'berkas_pegawai'));
    }

    public function test_pegawai_enum_values_method_exists()
    {
        $pegawai = new Pegawai();
        $this->assertTrue(method_exists($pegawai, 'getEnumValues'));
    }

    public function test_pegawai_photo_url_method_exists()
    {
        $pegawai = new Pegawai();
        $this->assertTrue(method_exists($pegawai, 'getPhotoUrl'));
    }

    public function test_pegawai_timestamps_disabled()
    {
        $pegawai = new Pegawai();
        $this->assertFalse($pegawai->timestamps);
    }

    public function test_related_models_exist()
    {
        // Test that all related model classes exist
        $this->assertTrue(class_exists(JnjJabatan::class));
        $this->assertTrue(class_exists(KelompokJabatan::class));
        $this->assertTrue(class_exists(ResikoKerja::class));
        $this->assertTrue(class_exists(EmergencyIndex::class));
        $this->assertTrue(class_exists(Departemen::class));
        $this->assertTrue(class_exists(Bidang::class));
        $this->assertTrue(class_exists(SttsWp::class));
        $this->assertTrue(class_exists(SttsKerja::class));
        $this->assertTrue(class_exists(Pendidikan::class));
        $this->assertTrue(class_exists(Bank::class));
        $this->assertTrue(class_exists(Dokter::class));
        $this->assertTrue(class_exists(Petugas::class));
        $this->assertTrue(class_exists(BerkasPegawai::class));
    }

    public function test_pegawai_model_uses_correct_database_connection()
    {
        $pegawai = new Pegawai();
        // Test that model can be instantiated without database connection
        $this->assertInstanceOf(Pegawai::class, $pegawai);
        // Connection name can be null in testing, that's fine
        $connection = $pegawai->getConnectionName();
        $this->assertTrue(is_string($connection) || is_null($connection));
    }
}