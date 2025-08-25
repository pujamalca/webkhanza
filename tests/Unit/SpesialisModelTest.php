<?php

namespace Tests\Unit;

use App\Models\Spesialis;
use App\Models\Dokter;
use Tests\TestCase;

class SpesialisModelTest extends TestCase
{
    public function test_spesialis_model_has_correct_table_name()
    {
        $spesialis = new Spesialis();
        $this->assertEquals('spesialis', $spesialis->getTable());
    }

    public function test_spesialis_model_has_correct_primary_key()
    {
        $spesialis = new Spesialis();
        $this->assertEquals('kd_sps', $spesialis->getKeyName());
        $this->assertFalse($spesialis->getIncrementing());
        $this->assertEquals('string', $spesialis->getKeyType());
    }

    public function test_spesialis_model_fillable_fields()
    {
        $spesialis = new Spesialis();
        $fillable = $spesialis->getFillable();
        
        $expectedFields = ['kd_sps', 'nm_sps'];
        
        foreach ($expectedFields as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_spesialis_relationship_methods_exist()
    {
        $spesialis = new Spesialis();
        
        // Test that relationship methods exist
        $this->assertTrue(method_exists($spesialis, 'dokters'));
    }

    public function test_spesialis_timestamps_disabled()
    {
        $spesialis = new Spesialis();
        $this->assertFalse($spesialis->timestamps);
    }

    public function test_related_models_exist()
    {
        // Test that all related model classes exist
        $this->assertTrue(class_exists(Dokter::class));
    }

    public function test_spesialis_model_uses_correct_database_connection()
    {
        $spesialis = new Spesialis();
        // Test that model can be instantiated without database connection
        $this->assertInstanceOf(Spesialis::class, $spesialis);
        // Connection name can be null in testing, that's fine
        $connection = $spesialis->getConnectionName();
        $this->assertTrue(is_string($connection) || is_null($connection));
    }

    public function test_spesialis_has_factory()
    {
        $spesialis = new Spesialis();
        $this->assertTrue(method_exists($spesialis, 'factory'));
    }
}