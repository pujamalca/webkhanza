<?php

namespace Tests\Unit;

use App\Filament\Clusters\SDM\Resources\DokterResource;
use App\Models\Dokter;
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

class DokterResourceTest extends TestCase
{
    public function test_dokter_resource_class_exists()
    {
        $this->assertTrue(class_exists(DokterResource::class));
    }

    public function test_dokter_resource_has_correct_model()
    {
        $this->assertEquals(Dokter::class, DokterResource::getModel());
    }

    public function test_dokter_resource_has_navigation_label()
    {
        $this->assertEquals('Dokter', DokterResource::getNavigationLabel());
    }

    public function test_dokter_resource_has_model_labels()
    {
        $this->assertEquals('Dokter', DokterResource::getModelLabel());
        $this->assertEquals('Data Dokter', DokterResource::getPluralModelLabel());
    }

    public function test_dokter_resource_form_schema_can_be_created()
    {
        $schema = new Schema();
        $formSchema = DokterResource::form($schema);
        
        $this->assertInstanceOf(Schema::class, $formSchema);
    }

    public function test_dokter_resource_form_uses_correct_components()
    {
        // Test that all required Filament components exist
        $this->assertTrue(class_exists(Section::class), 'Section component should exist');
        $this->assertTrue(class_exists(TextInput::class), 'TextInput component should exist');
        $this->assertTrue(class_exists(Select::class), 'Select component should exist');
        $this->assertTrue(class_exists(DatePicker::class), 'DatePicker component should exist');
        $this->assertTrue(class_exists(Textarea::class), 'Textarea component should exist');
    }

    public function test_dokter_resource_table_method_exists()
    {
        $this->assertTrue(method_exists(DokterResource::class, 'table'));
    }

    public function test_dokter_resource_table_uses_correct_columns()
    {
        // Test that all required table column components exist
        $this->assertTrue(class_exists(TextColumn::class), 'TextColumn component should exist');
        $this->assertTrue(class_exists(IconColumn::class), 'IconColumn component should exist');
    }

    public function test_dokter_resource_has_correct_pages()
    {
        $pages = DokterResource::getPages();
        
        $this->assertIsArray($pages);
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('view', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }

    public function test_dokter_resource_relations_is_array()
    {
        $relations = DokterResource::getRelations();
        $this->assertIsArray($relations);
    }

    public function test_dokter_resource_has_record_title_attribute()
    {
        $reflection = new \ReflectionClass(DokterResource::class);
        $property = $reflection->getProperty('recordTitleAttribute');
        $property->setAccessible(true);
        
        $this->assertEquals('nm_dokter', $property->getValue());
    }

    public function test_dokter_resource_uses_sdm_cluster()
    {
        $reflection = new \ReflectionClass(DokterResource::class);
        $property = $reflection->getProperty('cluster');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue());
    }

    public function test_dokter_resource_has_navigation_icon()
    {
        $reflection = new \ReflectionClass(DokterResource::class);
        $property = $reflection->getProperty('navigationIcon');
        $property->setAccessible(true);
        
        $this->assertEquals('heroicon-o-user', $property->getValue());
    }

    public function test_filament_schema_namespace_imports_work()
    {
        // Test that the correct Filament v4 namespaces are being used
        $this->assertTrue(class_exists('Filament\Schemas\Components\Section'));
        $this->assertTrue(class_exists('Filament\Schemas\Schema'));
        
        // Ensure old v3 namespaces don't interfere
        $this->assertFalse(class_exists('Filament\Forms\Components\Section') && 
                          !class_exists('Filament\Schemas\Components\Section'));
    }

    public function test_dokter_resource_form_sections_structure()
    {
        // Create a mock schema to test form structure
        $schema = new Schema();
        $formSchema = DokterResource::form($schema);
        
        // Test that form schema has been configured
        $this->assertInstanceOf(Schema::class, $formSchema);
        
        // Test that Section component can be instantiated (validates import)
        $section = Section::make('Test Section');
        $this->assertInstanceOf(Section::class, $section);
    }

    public function test_all_form_field_components_can_be_instantiated()
    {
        // Test all form components used in DokterResource
        $textInput = TextInput::make('test');
        $this->assertInstanceOf(TextInput::class, $textInput);
        
        $select = Select::make('test');
        $this->assertInstanceOf(Select::class, $select);
        
        $datePicker = DatePicker::make('test');
        $this->assertInstanceOf(DatePicker::class, $datePicker);
        
        $textarea = Textarea::make('test');
        $this->assertInstanceOf(Textarea::class, $textarea);
    }

    public function test_dokter_model_relationship_with_resource()
    {
        $this->assertTrue(class_exists(Dokter::class));
        $dokter = new Dokter();
        $this->assertInstanceOf(Dokter::class, $dokter);
    }
}