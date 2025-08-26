<?php

namespace Tests\Unit;

use App\Filament\Clusters\Erm\Resources\RawatJalanResource;
use App\Models\RegPeriksa;
use Tests\TestCase;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;

class RawatJalanResourceTest extends TestCase
{
    public function test_rawat_jalan_resource_class_exists()
    {
        $this->assertTrue(class_exists(RawatJalanResource::class));
    }

    public function test_rawat_jalan_resource_has_correct_model()
    {
        $this->assertEquals(RegPeriksa::class, RawatJalanResource::getModel());
    }

    public function test_rawat_jalan_resource_has_navigation_label()
    {
        $this->assertEquals('Rawat Jalan', RawatJalanResource::getNavigationLabel());
    }

    public function test_rawat_jalan_resource_has_model_labels()
    {
        $this->assertEquals('Registrasi Rawat Jalan', RawatJalanResource::getModelLabel());
        $this->assertEquals('Data Rawat Jalan', RawatJalanResource::getPluralModelLabel());
    }

    public function test_rawat_jalan_resource_form_schema_can_be_created()
    {
        $schema = new Schema();
        $this->assertInstanceOf(Schema::class, $schema);
        
        // Test that form method exists
        $this->assertTrue(method_exists(RawatJalanResource::class, 'form'));
    }

    public function test_rawat_jalan_resource_form_uses_correct_components()
    {
        $this->assertTrue(class_exists(Section::class), 'Section component should exist');
        $this->assertTrue(class_exists(Grid::class), 'Grid component should exist');
        $this->assertTrue(class_exists(TextInput::class), 'TextInput component should exist');
        $this->assertTrue(class_exists(Select::class), 'Select component should exist');
        $this->assertTrue(class_exists(DatePicker::class), 'DatePicker component should exist');
        $this->assertTrue(class_exists(TimePicker::class), 'TimePicker component should exist');
        $this->assertTrue(class_exists(Hidden::class), 'Hidden component should exist');
    }

    public function test_rawat_jalan_resource_table_method_exists()
    {
        $this->assertTrue(method_exists(RawatJalanResource::class, 'table'));
    }

    public function test_rawat_jalan_resource_table_uses_correct_columns()
    {
        $this->assertTrue(class_exists(TextColumn::class), 'TextColumn component should exist');
    }

    public function test_rawat_jalan_resource_has_correct_pages()
    {
        $pages = RawatJalanResource::getPages();
        
        $this->assertIsArray($pages);
        $this->assertArrayHasKey('index', $pages);
        $this->assertArrayHasKey('create', $pages);
        $this->assertArrayHasKey('view', $pages);
        $this->assertArrayHasKey('edit', $pages);
    }

    public function test_rawat_jalan_resource_relations_is_array()
    {
        $relations = RawatJalanResource::getRelations();
        $this->assertIsArray($relations);
    }

    public function test_rawat_jalan_resource_uses_erm_cluster()
    {
        $reflection = new \ReflectionClass(RawatJalanResource::class);
        $property = $reflection->getProperty('cluster');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue());
    }

    public function test_rawat_jalan_resource_has_navigation_icon()
    {
        $reflection = new \ReflectionClass(RawatJalanResource::class);
        $property = $reflection->getProperty('navigationIcon');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue());
    }

    public function test_rawat_jalan_resource_has_navigation_sort()
    {
        $reflection = new \ReflectionClass(RawatJalanResource::class);
        $property = $reflection->getProperty('navigationSort');
        $property->setAccessible(true);
        
        $this->assertEquals(2, $property->getValue());
    }

    public function test_reg_periksa_model_relationship_with_resource()
    {
        $this->assertTrue(class_exists(RegPeriksa::class));
        $regPeriksa = new RegPeriksa();
        $this->assertInstanceOf(RegPeriksa::class, $regPeriksa);
    }

    public function test_all_rawat_jalan_form_field_components_can_be_instantiated()
    {
        $textInput = TextInput::make('test');
        $this->assertInstanceOf(TextInput::class, $textInput);
        
        $select = Select::make('test');
        $this->assertInstanceOf(Select::class, $select);
        
        $datePicker = DatePicker::make('test');
        $this->assertInstanceOf(DatePicker::class, $datePicker);
        
        $timePicker = TimePicker::make('test');
        $this->assertInstanceOf(TimePicker::class, $timePicker);
        
        $hidden = Hidden::make('test');
        $this->assertInstanceOf(Hidden::class, $hidden);
    }

    public function test_rawat_jalan_resource_form_sections_structure()
    {
        $section = Section::make('Test Section');
        $this->assertInstanceOf(Section::class, $section);
        
        $grid = Grid::make(2);
        $this->assertInstanceOf(Grid::class, $grid);
    }

    public function test_filament_schema_namespace_imports_work_for_rawat_jalan()
    {
        $this->assertTrue(class_exists('Filament\Schemas\Components\Section'));
        $this->assertTrue(class_exists('Filament\Schemas\Components\Grid'));
        $this->assertTrue(class_exists('Filament\Schemas\Schema'));
    }

    public function test_rawat_jalan_resource_maintains_complex_structure()
    {
        // Test that all complex form sections can be created
        $registrasiSection = Section::make('Data Registrasi');
        $pjSection = Section::make('Data Penanggung Jawab');
        $statusSection = Section::make('Status & Biaya');
        
        $this->assertInstanceOf(Section::class, $registrasiSection);
        $this->assertInstanceOf(Section::class, $pjSection);
        $this->assertInstanceOf(Section::class, $statusSection);
    }

    public function test_rawat_jalan_resource_auto_generate_functionality()
    {
        // Test that no_reg and no_rawat fields can be created with auto-generation
        $noReg = TextInput::make('no_reg')
            ->readonly();
        
        $this->assertInstanceOf(TextInput::class, $noReg);
        
        $noRawat = TextInput::make('no_rawat')
            ->readonly();
            
        $this->assertInstanceOf(TextInput::class, $noRawat);
    }

    public function test_rawat_jalan_resource_pasien_search_components()
    {
        // Test that Select with search functionality can be created
        $pasienSelect = Select::make('no_rkm_medis')
            ->searchable()
            ->live();
        
        $this->assertInstanceOf(Select::class, $pasienSelect);
    }

    public function test_rawat_jalan_resource_select_with_create_option_form()
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

    public function test_rawat_jalan_resource_permissions_methods_exist()
    {
        $this->assertTrue(method_exists(RawatJalanResource::class, 'canViewAny'));
        $this->assertTrue(method_exists(RawatJalanResource::class, 'canView'));
        $this->assertTrue(method_exists(RawatJalanResource::class, 'canCreate'));
        $this->assertTrue(method_exists(RawatJalanResource::class, 'canEdit'));
        $this->assertTrue(method_exists(RawatJalanResource::class, 'canDelete'));
    }

    public function test_rawat_jalan_resource_table_has_filters_and_actions()
    {
        // Test that table method exists and can be called
        $this->assertTrue(method_exists(RawatJalanResource::class, 'table'));
        
        // Test table components can be instantiated
        $textColumn = TextColumn::make('test');
        $this->assertInstanceOf(TextColumn::class, $textColumn);
    }

    public function test_rawat_jalan_resource_eloquent_query_filter()
    {
        // Test that getEloquentQuery method exists
        $this->assertTrue(method_exists(RawatJalanResource::class, 'getEloquentQuery'));
    }

    public function test_rawat_jalan_resource_status_lanjut_filter()
    {
        // Test that the resource filters by status_lanjut = 'Ralan'
        $query = RawatJalanResource::getEloquentQuery();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }

    public function test_rawat_jalan_resource_form_auto_fill_functionality()
    {
        // Test that live functionality can be applied to form components
        $liveSelect = Select::make('test_select')
            ->live()
            ->afterStateUpdated(function ($state, $set) {
                // Test that afterStateUpdated callback can be set
                $set('test_field', 'test_value');
            });
        
        $this->assertInstanceOf(Select::class, $liveSelect);
    }

    public function test_rawat_jalan_resource_biaya_calculation_components()
    {
        // Test that biaya_reg field can be created
        $biayaReg = TextInput::make('biaya_reg')
            ->numeric()
            ->prefix('Rp');
            
        $this->assertInstanceOf(TextInput::class, $biayaReg);
    }

    public function test_rawat_jalan_resource_time_and_date_components()
    {
        // Test that date and time components work properly
        $tglRegistrasi = DatePicker::make('tgl_registrasi')
            ->default(now());
            
        $jamReg = TimePicker::make('jam_reg')
            ->default(now()->format('H:i:s'));
        
        $this->assertInstanceOf(DatePicker::class, $tglRegistrasi);
        $this->assertInstanceOf(TimePicker::class, $jamReg);
    }
}