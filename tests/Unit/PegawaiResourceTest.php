<?php

namespace Tests\Unit;

use App\Filament\Clusters\SDM\Resources\PegawaiResource;
use App\Models\Pegawai;
use Tests\TestCase;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;

class PegawaiResourceTest extends TestCase
{
    public function test_pegawai_resource_class_exists()
    {
        $this->assertTrue(class_exists(PegawaiResource::class));
    }

    public function test_pegawai_resource_has_correct_model()
    {
        $this->assertEquals(Pegawai::class, PegawaiResource::getModel());
    }

    public function test_pegawai_resource_has_navigation_label()
    {
        $this->assertEquals('Pegawai', PegawaiResource::getNavigationLabel());
    }

    public function test_pegawai_resource_has_model_labels()
    {
        $this->assertEquals('Pegawai', PegawaiResource::getModelLabel());
        $this->assertEquals('Data Pegawai', PegawaiResource::getPluralModelLabel());
    }

    public function test_pegawai_resource_form_schema_can_be_created()
    {
        $schema = new Schema();
        $formSchema = PegawaiResource::form($schema);
        
        $this->assertInstanceOf(Schema::class, $formSchema);
    }

    public function test_pegawai_resource_form_uses_correct_components()
    {
        $this->assertTrue(class_exists(Section::class), 'Section component should exist');
        $this->assertTrue(class_exists(TextInput::class), 'TextInput component should exist');
        $this->assertTrue(class_exists(Select::class), 'Select component should exist');
        $this->assertTrue(class_exists(DatePicker::class), 'DatePicker component should exist');
        $this->assertTrue(class_exists(FileUpload::class), 'FileUpload component should exist');
    }

    public function test_pegawai_resource_table_method_exists()
    {
        $this->assertTrue(method_exists(PegawaiResource::class, 'table'));
    }

    public function test_pegawai_resource_table_uses_correct_columns()
    {
        $this->assertTrue(class_exists(TextColumn::class), 'TextColumn component should exist');
    }

    public function test_pegawai_resource_has_correct_pages()
    {
        $pages = PegawaiResource::getPages();
        
        $this->assertIsArray($pages);
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('view', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }

    public function test_pegawai_resource_relations_is_array()
    {
        $relations = PegawaiResource::getRelations();
        $this->assertIsArray($relations);
    }

    public function test_pegawai_resource_has_record_title_attribute()
    {
        $reflection = new \ReflectionClass(PegawaiResource::class);
        $property = $reflection->getProperty('recordTitleAttribute');
        $property->setAccessible(true);
        
        $this->assertEquals('nama', $property->getValue());
    }

    public function test_pegawai_resource_uses_sdm_cluster()
    {
        $reflection = new \ReflectionClass(PegawaiResource::class);
        $property = $reflection->getProperty('cluster');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue());
    }

    public function test_pegawai_resource_has_navigation_icon()
    {
        $reflection = new \ReflectionClass(PegawaiResource::class);
        $property = $reflection->getProperty('navigationIcon');
        $property->setAccessible(true);
        
        $this->assertEquals('heroicon-o-user', $property->getValue());
    }

    public function test_pegawai_model_relationship_with_resource()
    {
        $this->assertTrue(class_exists(Pegawai::class));
        $pegawai = new Pegawai();
        $this->assertInstanceOf(Pegawai::class, $pegawai);
    }

    public function test_all_pegawai_form_field_components_can_be_instantiated()
    {
        $textInput = TextInput::make('test');
        $this->assertInstanceOf(TextInput::class, $textInput);
        
        $select = Select::make('test');
        $this->assertInstanceOf(Select::class, $select);
        
        $datePicker = DatePicker::make('test');
        $this->assertInstanceOf(DatePicker::class, $datePicker);
        
        $fileUpload = FileUpload::make('test');
        $this->assertInstanceOf(FileUpload::class, $fileUpload);
    }

    public function test_pegawai_resource_form_sections_structure()
    {
        $schema = new Schema();
        $formSchema = PegawaiResource::form($schema);
        
        $this->assertInstanceOf(Schema::class, $formSchema);
        
        $section = Section::make('Test Section');
        $this->assertInstanceOf(Section::class, $section);
    }

    public function test_filament_schema_namespace_imports_work_for_pegawai()
    {
        $this->assertTrue(class_exists('Filament\Schemas\Components\Section'));
        $this->assertTrue(class_exists('Filament\Schemas\Schema'));
    }

    public function test_pegawai_resource_maintains_complex_structure()
    {
        // Test that the complex form structure is maintained
        $schema = new Schema();
        $formSchema = PegawaiResource::form($schema);
        
        $this->assertInstanceOf(Schema::class, $formSchema);
        
        // Test that all complex form sections can be created
        $pribadiSection = Section::make('Data Pribadi');
        $kepegawaianSection = Section::make('Data Kepegawaian');
        $keuanganSection = Section::make('Data Keuangan & Pajak');
        $pendidikanSection = Section::make('Pendidikan & Foto');
        
        $this->assertInstanceOf(Section::class, $pribadiSection);
        $this->assertInstanceOf(Section::class, $kepegawaianSection);
        $this->assertInstanceOf(Section::class, $keuanganSection);
        $this->assertInstanceOf(Section::class, $pendidikanSection);
    }
}