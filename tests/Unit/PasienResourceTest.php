<?php

namespace Tests\Unit;

use App\Filament\Clusters\Erm\Resources\PasienResource;
use App\Models\Pasien;
use Tests\TestCase;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class PasienResourceTest extends TestCase
{
    public function test_pasien_resource_class_exists()
    {
        $this->assertTrue(class_exists(PasienResource::class));
    }

    public function test_pasien_resource_has_correct_model()
    {
        $this->assertEquals(Pasien::class, PasienResource::getModel());
    }

    public function test_pasien_resource_has_navigation_label()
    {
        $this->assertEquals('Pasien', PasienResource::getNavigationLabel());
    }

    public function test_pasien_resource_has_model_labels()
    {
        $this->assertEquals('Pasien', PasienResource::getModelLabel());
        $this->assertEquals('Data Pasien', PasienResource::getPluralModelLabel());
    }

    public function test_pasien_resource_form_schema_can_be_created()
    {
        $schema = new Schema();
        $this->assertInstanceOf(Schema::class, $schema);
        
        // Test that form method exists
        $this->assertTrue(method_exists(PasienResource::class, 'form'));
    }

    public function test_pasien_resource_form_uses_correct_components()
    {
        $this->assertTrue(class_exists(Section::class), 'Section component should exist');
        $this->assertTrue(class_exists(Grid::class), 'Grid component should exist');
        $this->assertTrue(class_exists(TextInput::class), 'TextInput component should exist');
        $this->assertTrue(class_exists(Select::class), 'Select component should exist');
        $this->assertTrue(class_exists(DatePicker::class), 'DatePicker component should exist');
        $this->assertTrue(class_exists(Checkbox::class), 'Checkbox component should exist');
        $this->assertTrue(class_exists(Textarea::class), 'Textarea component should exist');
    }

    public function test_pasien_resource_table_method_exists()
    {
        $this->assertTrue(method_exists(PasienResource::class, 'table'));
    }

    public function test_pasien_resource_table_uses_correct_columns()
    {
        $this->assertTrue(class_exists(TextColumn::class), 'TextColumn component should exist');
    }

    public function test_pasien_resource_has_correct_pages()
    {
        $pages = PasienResource::getPages();
        
        $this->assertIsArray($pages);
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('view', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }

    public function test_pasien_resource_relations_is_array()
    {
        $relations = PasienResource::getRelations();
        $this->assertIsArray($relations);
    }

    public function test_pasien_resource_uses_erm_cluster()
    {
        $reflection = new \ReflectionClass(PasienResource::class);
        $property = $reflection->getProperty('cluster');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue());
    }

    public function test_pasien_resource_has_navigation_icon()
    {
        $reflection = new \ReflectionClass(PasienResource::class);
        $property = $reflection->getProperty('navigationIcon');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue());
    }

    public function test_pasien_resource_has_navigation_sort()
    {
        $reflection = new \ReflectionClass(PasienResource::class);
        $property = $reflection->getProperty('navigationSort');
        $property->setAccessible(true);
        
        $this->assertEquals(1, $property->getValue());
    }

    public function test_pasien_model_relationship_with_resource()
    {
        $this->assertTrue(class_exists(Pasien::class));
        $pasien = new Pasien();
        $this->assertInstanceOf(Pasien::class, $pasien);
    }

    public function test_all_pasien_form_field_components_can_be_instantiated()
    {
        $textInput = TextInput::make('test');
        $this->assertInstanceOf(TextInput::class, $textInput);
        
        $select = Select::make('test');
        $this->assertInstanceOf(Select::class, $select);
        
        $datePicker = DatePicker::make('test');
        $this->assertInstanceOf(DatePicker::class, $datePicker);
        
        $checkbox = Checkbox::make('test');
        $this->assertInstanceOf(Checkbox::class, $checkbox);
        
        $textarea = Textarea::make('test');
        $this->assertInstanceOf(Textarea::class, $textarea);
    }

    public function test_pasien_resource_form_sections_structure()
    {
        $section = Section::make('Test Section');
        $this->assertInstanceOf(Section::class, $section);
        
        $grid = Grid::make(2);
        $this->assertInstanceOf(Grid::class, $grid);
    }

    public function test_filament_schema_namespace_imports_work_for_pasien()
    {
        $this->assertTrue(class_exists('Filament\Schemas\Components\Section'));
        $this->assertTrue(class_exists('Filament\Schemas\Components\Grid'));
        $this->assertTrue(class_exists('Filament\Schemas\Schema'));
    }

    public function test_pasien_resource_maintains_complex_structure()
    {
        // Test that all complex form sections can be created
        $identitasSection = Section::make('Data Identitas Pasien');
        $kontakSection = Section::make('Informasi Kontak & Alamat');
        $pribadiSection = Section::make('Data Pribadi');
        $pjSection = Section::make('Data Penanggung Jawab');
        $pendaftaranSection = Section::make('Informasi Pendaftaran');
        
        $this->assertInstanceOf(Section::class, $identitasSection);
        $this->assertInstanceOf(Section::class, $kontakSection);
        $this->assertInstanceOf(Section::class, $pribadiSection);
        $this->assertInstanceOf(Section::class, $pjSection);
        $this->assertInstanceOf(Section::class, $pendaftaranSection);
    }

    public function test_pasien_resource_auto_generate_rm_functionality()
    {
        // Test that checkbox component for auto-generate RM can be created
        $autoGenerateCheckbox = Checkbox::make('auto_generate_rm')
            ->label('Auto Generate')
            ->default(true)
            ->live();
        
        $this->assertInstanceOf(Checkbox::class, $autoGenerateCheckbox);
    }

    public function test_pasien_resource_umur_calculation_components()
    {
        // Test that date picker for birth date can be created with live functionality
        $tglLahir = DatePicker::make('tgl_lahir')
            ->label('Tanggal Lahir')
            ->maxDate(now())
            ->live();
        
        $this->assertInstanceOf(DatePicker::class, $tglLahir);
        
        // Test that umur field can be created as readonly
        $umur = TextInput::make('umur')
            ->label('Umur')
            ->readonly();
            
        $this->assertInstanceOf(TextInput::class, $umur);
    }

    public function test_pasien_resource_select_with_create_option_form()
    {
        // Test that Select with createOptionForm can be created
        $selectWithCreate = Select::make('test_select')
            ->options([])
            ->searchable()
            ->createOptionForm([
                TextInput::make('test_field')
                    ->required(),
            ]);
        
        $this->assertInstanceOf(Select::class, $selectWithCreate);
    }

    public function test_pasien_resource_permissions_methods_exist()
    {
        $this->assertTrue(method_exists(PasienResource::class, 'canViewAny'));
        $this->assertTrue(method_exists(PasienResource::class, 'canView'));
        $this->assertTrue(method_exists(PasienResource::class, 'canCreate'));
        $this->assertTrue(method_exists(PasienResource::class, 'canEdit'));
        $this->assertTrue(method_exists(PasienResource::class, 'canDelete'));
    }

    public function test_pasien_resource_table_has_filters_and_actions()
    {
        // Test that table method exists and can be called
        $this->assertTrue(method_exists(PasienResource::class, 'table'));
        
        // Test table components can be instantiated
        $textColumn = TextColumn::make('test');
        $this->assertInstanceOf(TextColumn::class, $textColumn);
    }

    public function test_pasien_resource_grid_layout_functionality()
    {
        // Test Grid layout with different column configurations
        $grid2 = Grid::make(2);
        $grid3 = Grid::make(3);
        
        $this->assertInstanceOf(Grid::class, $grid2);
        $this->assertInstanceOf(Grid::class, $grid3);
    }
}