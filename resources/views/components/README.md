# Universal Livewire Components System

Sistem komponen universal untuk semua Livewire components di WebKhanza yang menggunakan Tailwind CSS dengan Vite integration.

## 🎯 Best Practices

### 1. Performance Optimization
- **@once directive**: Vite assets dan scripts hanya dimuat sekali per halaman
- **Lazy loading**: Theme management scripts dibuat modular
- **CSS optimizations**: Menggunakan Tailwind @apply untuk consistent styling

### 2. Code Reusability
- **Universal components**: Bisa digunakan di semua Livewire forms
- **Modular structure**: Setiap component punya responsibility masing-masing
- **Configurable**: Props untuk customization tanpa duplicate code

### 3. Developer Experience
- **Semantic naming**: `.livewire-*` classes untuk mudah debugging
- **Auto error handling**: Built-in validation states
- **Dark mode ready**: Automatic theme switching dengan Filament

## 📦 Available Components

### 1. `<x-livewire-layout>`
Container utama untuk semua Livewire pages.

```blade
<x-livewire-layout title="Page Title" container="max-w-4xl">
    <!-- Your content here -->
</x-livewire-layout>
```

**Props:**
- `title`: Page title (optional)
- `container`: Container width class (default: max-w-7xl)
- `padding`: Vertical padding (default: py-8)
- `theme`: Theme mode - auto/light/dark (default: auto)

### 2. `<x-livewire-form>`
Form wrapper dengan grid support dan Livewire integration.

```blade
<x-livewire-form wire:submit="save" grid columns="3">
    <!-- Form fields here -->
</x-livewire-form>
```

**Props:**
- `wire:submit`: Livewire submit method
- `grid`: Enable grid layout (boolean)
- `columns`: Grid columns - 1/2/3/auto (default: 1)
- `gap`: Grid gap size (default: 6)

### 3. `<x-livewire-field>`
Universal form field component dengan auto error handling.

```blade
{{-- Text Input --}}
<x-livewire-field
    type="text"
    label="Name"
    wire:model="name"
    required />

{{-- Textarea --}}
<x-livewire-field
    type="textarea"
    label="Description"
    wire:model="description"
    rows="4" />

{{-- Select --}}
<x-livewire-field
    type="select"
    label="Category"
    wire:model="category"
    :options="$categories"
    placeholder="Choose category..." />

{{-- Custom Label dengan Slot --}}
<x-livewire-field type="textarea" wire:model="notes">
    <x-slot name="label">
        <span class="text-blue-500">📝</span> Special Notes
    </x-slot>
</x-livewire-field>
```

**Props:**
- `type`: Field type - text/textarea/select/date/time/email/etc
- `label`: Field label
- `required`: Mark as required (boolean)
- `readonly`: Make readonly (boolean)
- `placeholder`: Placeholder text
- `wire:model`: Livewire model binding
- `options`: Array untuk select options
- `rows`: Textarea rows (default: 4)
- `error`: Custom error message

### 4. `<x-livewire-section>`
Section wrapper dengan title dan subtitle.

```blade
<x-livewire-section title="User Information" subtitle="Basic user details">
    <!-- Section content -->
</x-livewire-section>
```

**Props:**
- `title`: Section title
- `subtitle`: Section subtitle
- `padding`: Section padding (default: p-6)

## 🚀 Usage Examples

### Simple Form
```blade
<x-livewire-layout title="Create User">
    <x-livewire-form wire:submit="save">
        <x-livewire-field type="text" label="Name" wire:model="name" required />
        <x-livewire-field type="email" label="Email" wire:model="email" required />

        <div class="livewire-button-group">
            <x-filament::button type="submit">Save User</x-filament::button>
        </div>
    </x-livewire-form>
</x-livewire-layout>
```

### Grid Form
```blade
<x-livewire-layout title="Patient Registration">
    <x-livewire-form wire:submit="register" grid columns="2">
        <x-livewire-field type="text" label="First Name" wire:model="first_name" required />
        <x-livewire-field type="text" label="Last Name" wire:model="last_name" required />
        <x-livewire-field type="date" label="Birth Date" wire:model="birth_date" required />
        <x-livewire-field type="select" label="Gender" wire:model="gender" :options="$genders" required />
    </x-livewire-form>
</x-livewire-layout>
```

### Multi-Section Form
```blade
<x-livewire-layout title="Medical Record">
    <x-livewire-form wire:submit="save">

        <x-livewire-section title="Patient Info" subtitle="Basic information">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-livewire-field type="text" label="Patient ID" wire:model="patient_id" readonly />
                <x-livewire-field type="text" label="Name" wire:model="name" required />
            </div>
        </x-livewire-section>

        <x-livewire-section title="Medical Assessment" subtitle="SOAP notes">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-livewire-field type="textarea" label="Subjective" wire:model="subjective" />
                <x-livewire-field type="textarea" label="Objective" wire:model="objective" />
            </div>
        </x-livewire-section>

    </x-livewire-form>
</x-livewire-layout>
```

## 🎨 Available CSS Classes

### Layout Classes
- `.livewire-container` - Main container
- `.livewire-wrapper` - Content wrapper
- `.livewire-section` - Section wrapper
- `.livewire-card` - Card container

### Form Classes
- `.livewire-form` - Form container
- `.livewire-input` - Input fields
- `.livewire-textarea` - Textarea fields
- `.livewire-select` - Select fields
- `.livewire-label` - Field labels
- `.livewire-error` - Error messages
- `.livewire-button-group` - Button container

### Grid Classes
- `.livewire-grid` - Basic grid
- `.livewire-grid-responsive` - Responsive grid

### Utility Classes
- `.livewire-fade-in` - Fade in animation
- `.livewire-hidden/.livewire-visible` - Visibility utilities

## 🌙 Dark Mode

Dark mode otomatis mengikuti Filament theme system. Tidak perlu konfigurasi tambahan.

## ⚡ Performance Features

1. **@once Loading**: Vite assets dimuat sekali per halaman
2. **CSS Optimizations**: Menggunakan Tailwind @apply untuk efficiency
3. **JavaScript Lazy Loading**: Theme management dimuat on-demand
4. **Auto Error Handling**: Built-in validation tanpa extra code

## 🔧 Customization

### Custom Field Types
```blade
{{-- Custom input dengan validation --}}
<x-livewire-field
    type="number"
    label="Age"
    wire:model="age"
    min="0"
    max="150"
    required />

{{-- Custom select dengan complex options --}}
<x-livewire-field type="select" label="Department" wire:model="department_id">
    @foreach($departments as $dept)
        <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
    @endforeach
</x-livewire-field>
```

### Custom Styling
```blade
{{-- Override default classes --}}
<x-livewire-field
    type="text"
    label="Special Field"
    wire:model="special"
    class="livewire-input border-blue-500 focus:ring-blue-600" />
```

## 📝 Migration Guide

### From Old System
```blade
{{-- OLD --}}
<div class="soapie-container">
    <form class="soapie-form">
        <input class="soapie-input" />
    </form>
</div>

{{-- NEW --}}
<x-livewire-layout>
    <x-livewire-form>
        <x-livewire-field type="text" />
    </x-livewire-form>
</x-livewire-layout>
```

### Benefits of Migration
- ✅ Faster loading dengan @once
- ✅ Consistent styling
- ✅ Auto error handling
- ✅ Better maintainability
- ✅ Dark mode ready
- ✅ Mobile responsive