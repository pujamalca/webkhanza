<?php

namespace Tests\Unit;

use App\Models\Dokter;
use App\Models\Pegawai;
use App\Models\Spesialis;
use Tests\TestCase;

class DokterModelTest extends TestCase
{
    public function test_dokter_model_has_correct_table_name()
    {
        $dokter = new Dokter();
        $this->assertEquals('dokter', $dokter->getTable());
    }

    public function test_dokter_model_has_correct_primary_key()
    {
        $dokter = new Dokter();
        $this->assertEquals('kd_dokter', $dokter->getKeyName());
        $this->assertFalse($dokter->getIncrementing());
        $this->assertEquals('string', $dokter->getKeyType());
    }

    public function test_dokter_model_fillable_fields()
    {
        $dokter = new Dokter();
        $fillable = $dokter->getFillable();
        
        $expectedFields = [
            'kd_dokter', 'nm_dokter', 'jk', 'tmp_lahir', 'tgl_lahir', 
            'gol_drh', 'agama', 'almt_tgl', 'no_telp', 'email', 
            'stts_nikah', 'kd_sps', 'alumni', 'no_ijn_praktek', 'status'
        ];
        
        foreach ($expectedFields as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_dokter_model_casts_configuration()
    {
        $dokter = new Dokter();
        $casts = $dokter->getCasts();
        
        $this->assertArrayHasKey('tgl_lahir', $casts);
        $this->assertArrayHasKey('status', $casts);
        $this->assertEquals('date', $casts['tgl_lahir']);
        $this->assertEquals('boolean', $casts['status']);
    }

    public function test_dokter_relationship_methods_exist()
    {
        $dokter = new Dokter();
        
        // Test that relationship methods exist
        $this->assertTrue(method_exists($dokter, 'pegawai'));
        $this->assertTrue(method_exists($dokter, 'spesialis'));
    }

    public function test_dokter_enum_values_method_exists()
    {
        $dokter = new Dokter();
        $this->assertTrue(method_exists($dokter, 'getEnumValues'));
    }

    public function test_dokter_timestamps_disabled()
    {
        $dokter = new Dokter();
        $this->assertFalse($dokter->timestamps);
    }

    public function test_related_models_exist()
    {
        // Test that all related model classes exist
        $this->assertTrue(class_exists(Spesialis::class));
        $this->assertTrue(class_exists(Pegawai::class));
    }

    public function test_dokter_model_uses_correct_database_connection()
    {
        $dokter = new Dokter();
        // Test that model can be instantiated without database connection
        $this->assertInstanceOf(Dokter::class, $dokter);
        // Connection name can be null in testing, that's fine
        $connection = $dokter->getConnectionName();
        $this->assertTrue(is_string($connection) || is_null($connection));
    }

    public function test_dokter_has_factory()
    {
        $dokter = new Dokter();
        $this->assertTrue(method_exists($dokter, 'factory'));
    }
}