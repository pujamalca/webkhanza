<?php

namespace Tests\Unit;

use App\Models\Pegawai;
use Tests\TestCase;

class SimplePegawaiTest extends TestCase
{
    public function test_pegawai_model_exists()
    {
        $this->assertTrue(class_exists(Pegawai::class));
    }

    public function test_pegawai_has_correct_table_name()
    {
        $pegawai = new Pegawai();
        $this->assertEquals('pegawai', $pegawai->getTable());
    }

    public function test_pegawai_has_correct_fillable_fields()
    {
        $pegawai = new Pegawai();
        $fillable = $pegawai->getFillable();
        
        $this->assertContains('nik', $fillable);
        $this->assertContains('nama', $fillable);
        $this->assertContains('jk', $fillable);
    }

    public function test_pegawai_has_required_casts()
    {
        $pegawai = new Pegawai();
        $casts = $pegawai->getCasts();
        
        $this->assertArrayHasKey('tgl_lahir', $casts);
        $this->assertArrayHasKey('mulai_kerja', $casts);
    }
}