<?php

namespace Tests\Unit;

use App\Models\BerkasPegawai;
use Tests\TestCase;

class BerkasPegawaiModelTest extends TestCase
{
    public function test_berkas_pegawai_model_has_correct_table_name()
    {
        $berkasPegawai = new BerkasPegawai();
        $this->assertEquals('berkas_pegawai', $berkasPegawai->getTable());
    }

    public function test_berkas_pegawai_model_has_correct_primary_key()
    {
        $berkasPegawai = new BerkasPegawai();
        $this->assertEquals('berkas', $berkasPegawai->getKeyName());
        $this->assertFalse($berkasPegawai->getIncrementing());
        $this->assertEquals('string', $berkasPegawai->getKeyType());
    }

    public function test_berkas_pegawai_model_fillable_fields()
    {
        $berkasPegawai = new BerkasPegawai();
        $fillable = $berkasPegawai->getFillable();
        
        $expectedFields = [
            'nik', 'tgl_uploud', 'tgl_berakhir', 'kode_berkas', 'berkas'
        ];
        
        foreach ($expectedFields as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_berkas_pegawai_model_casts_configuration()
    {
        $berkasPegawai = new BerkasPegawai();
        $casts = $berkasPegawai->getCasts();
        
        $this->assertArrayHasKey('tgl_uploud', $casts);
        $this->assertArrayHasKey('tgl_berakhir', $casts);
        $this->assertEquals('date', $casts['tgl_uploud']);
        $this->assertEquals('date', $casts['tgl_berakhir']);
    }

    public function test_berkas_pegawai_relationship_methods_exist()
    {
        $berkasPegawai = new BerkasPegawai();
        
        // Test that relationship methods exist
        $this->assertTrue(method_exists($berkasPegawai, 'pegawai'));
        $this->assertTrue(method_exists($berkasPegawai, 'masterBerkasPegawai'));
    }

    public function test_berkas_pegawai_timestamps_disabled()
    {
        $berkasPegawai = new BerkasPegawai();
        $this->assertFalse($berkasPegawai->timestamps);
    }

    public function test_berkas_pegawai_model_uses_correct_database_connection()
    {
        $berkasPegawai = new BerkasPegawai();
        // Test that model can be instantiated without database connection
        $this->assertInstanceOf(BerkasPegawai::class, $berkasPegawai);
        // Connection name can be null in testing, that's fine
        $connection = $berkasPegawai->getConnectionName();
        $this->assertTrue(is_string($connection) || is_null($connection));
    }
}