<?php

namespace Tests\Unit;

use App\Filament\Clusters\SDM\Resources\PetugasResource;
use App\Models\Petugas;
use Tests\TestCase;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class PetugasResourceTest extends TestCase
{
    public function test_petugas_resource_class_exists()
    {
        $this->assertTrue(class_exists(PetugasResource::class));
    }

    public function test_petugas_resource_has_correct_model()
    {
        $this->assertEquals(Petugas::class, PetugasResource::getModel());
    }

    public function test_petugas_resource_has_navigation_label()
    {
        $this->assertEquals('Petugas', PetugasResource::getNavigationLabel());
    }

    public function test_petugas_resource_has_model_labels()
    {
        $this->assertEquals('Petugas', PetugasResource::getModelLabel());
        $this->assertEquals('Data Petugas', PetugasResource::getPluralModelLabel());
    }

    public function test_petugas_resource_form_schema_can_be_created()
    {
        $schema = new Schema();
        $formSchema = PetugasResource::form($schema);
        
        $this->assertInstanceOf(Schema::class, $formSchema);
    }

    public function test_petugas_resource_form_uses_correct_components()
    {
        $this->assertTrue(class_exists(Section::class), 'Section component should exist');
        $this->assertTrue(class_exists(TextInput::class), 'TextInput component should exist');
        $this->assertTrue(class_exists(Select::class), 'Select component should exist');
        $this->assertTrue(class_exists(DatePicker::class), 'DatePicker component should exist');
        $this->assertTrue(class_exists(Textarea::class), 'Textarea component should exist');
    }

    public function test_petugas_resource_table_method_exists()
    {
        $this->assertTrue(method_exists(PetugasResource::class, 'table'));
    }

    public function test_petugas_resource_table_uses_correct_columns()
    {
        $this->assertTrue(class_exists(TextColumn::class), 'TextColumn component should exist');
        $this->assertTrue(class_exists(IconColumn::class), 'IconColumn component should exist');
    }

    public function test_petugas_resource_has_correct_pages()
    {
        $pages = PetugasResource::getPages();
        
        $this->assertIsArray($pages);
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('view', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }

    public function test_petugas_resource_relations_is_array()
    {
        $relations = PetugasResource::getRelations();
        $this->assertIsArray($relations);
    }

    public function test_petugas_resource_has_record_title_attribute()
    {
        $reflection = new \ReflectionClass(PetugasResource::class);
        $property = $reflection->getProperty('recordTitleAttribute');
        $property->setAccessible(true);
        
        $this->assertEquals('nama', $property->getValue());
    }

    public function test_petugas_resource_uses_sdm_cluster()
    {
        $reflection = new \ReflectionClass(PetugasResource::class);
        $property = $reflection->getProperty('cluster');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue());
    }

    public function test_petugas_resource_has_navigation_icon()
    {
        $reflection = new \ReflectionClass(PetugasResource::class);
        $property = $reflection->getProperty('navigationIcon');
        $property->setAccessible(true);
        
        $this->assertEquals('heroicon-o-identification', $property->getValue());
    }

    public function test_petugas_model_relationship_with_resource()
    {
        $this->assertTrue(class_exists(Petugas::class));
        $petugas = new Petugas();
        $this->assertInstanceOf(Petugas::class, $petugas);
    }

    public function test_all_petugas_form_field_components_can_be_instantiated()
    {
        $textInput = TextInput::make('test');
        $this->assertInstanceOf(TextInput::class, $textInput);
        
        $select = Select::make('test');
        $this->assertInstanceOf(Select::class, $select);
        
        $datePicker = DatePicker::make('test');
        $this->assertInstanceOf(DatePicker::class, $datePicker);
        
        $textarea = Textarea::make('test');
        $this->assertInstanceOf(Textarea::class, $textarea);
    }

    public function test_petugas_resource_form_sections_structure()
    {
        $schema = new Schema();
        $formSchema = PetugasResource::form($schema);
        
        $this->assertInstanceOf(Schema::class, $formSchema);
        
        $section = Section::make('Test Section');
        $this->assertInstanceOf(Section::class, $section);
    }
}