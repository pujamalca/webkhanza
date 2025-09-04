<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Services\WebsiteThemeService;

/**
 * Model untuk menyimpan identitas website
 * 
 * Pattern Singleton - hanya boleh ada satu record dalam database
 * 
 * @property int $id
 * @property string $name Nama website
 * @property string $description Deskripsi singkat website
 * @property string|null $logo Path file logo website
 * @property string|null $favicon Path file favicon website
 * @property string $email Email kontak
 * @property string $phone Nomor telepon
 * @property string $address Alamat lengkap
 * @property string $tagline Tagline atau motto website
 * @property string $primary_color Warna utama website (hex code)
 * @property string $secondary_color Warna sekunder website (hex code)  
 * @property string $accent_color Warna aksen website (hex code)
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class WebsiteIdentity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'logo',
        'favicon',
        'email',
        'phone',
        'address',
        'tagline',
        'primary_color',
        'secondary_color',
        'accent_color',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the singleton instance of website identity
     * 
     * @return static
     */
    public static function getInstance(): static
    {
        try {
            $instance = static::first();
        } catch (\Exception $e) {
            // Handle case when table doesn't exist yet (during migration)
            if (str_contains($e->getMessage(), 'Table') && str_contains($e->getMessage(), 'doesn\'t exist')) {
                return static::getDefaultInstance();
            }
            throw $e;
        }
        
        if (!$instance) {
            // Create default instance if not exists
            $instance = static::create([
                'name' => 'WebKhanza',
                'description' => 'Sistem Manajemen Pegawai dan Absensi',
                'email' => 'admin@webkhanza.local',
                'phone' => '021-12345678',
                'address' => 'Jalan Contoh No. 123, Jakarta, Indonesia',
                'tagline' => 'Sistem Terpadu untuk Manajemen Pegawai',
                'primary_color' => '#3B82F6',
                'secondary_color' => '#1E40AF',
                'accent_color' => '#EF4444',
            ]);
        }
        
        return $instance;
    }

    /**
     * Get default instance when table doesn't exist
     * 
     * @return static
     */
    public static function getDefaultInstance(): static
    {
        $instance = new static();
        $instance->name = 'WebKhanza';
        $instance->description = 'Sistem Manajemen Pegawai dan Absensi';
        $instance->email = 'admin@webkhanza.local';
        $instance->phone = '021-12345678';
        $instance->address = 'Jalan Contoh No. 123, Jakarta, Indonesia';
        $instance->tagline = 'Sistem Terpadu untuk Manajemen Pegawai';
        $instance->colors = [
            'primary_color' => '#3B82F6',
            'secondary_color' => '#1E40AF',
            'accent_color' => '#F59E0B'
        ];
        
        return $instance;
    }

    /**
     * Get full URL for logo image
     * 
     * @return string|null
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return Storage::disk('public')->url($this->logo);
    }

    /**
     * Get full URL for favicon image
     * 
     * @return string|null
     */
    public function getFaviconUrlAttribute(): ?string
    {
        if (!$this->favicon) {
            return null;
        }

        return Storage::disk('public')->url($this->favicon);
    }

    /**
     * Delete old logo file when updating
     * 
     * @param string $newLogo
     * @return void
     */
    public function updateLogo(string $newLogo): void
    {
        // Delete old logo if exists
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            Storage::disk('public')->delete($this->logo);
        }

        $this->update(['logo' => $newLogo]);
    }

    /**
     * Delete old favicon file when updating
     * 
     * @param string|null $newFavicon
     * @return void
     */
    public function updateFavicon(?string $newFavicon): void
    {
        // Delete old favicon if exists
        if ($this->favicon && Storage::disk('public')->exists($this->favicon)) {
            Storage::disk('public')->delete($this->favicon);
        }

        $this->update(['favicon' => $newFavicon]);
    }

    /**
     * Boot the model
     * 
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        // Prevent multiple records (singleton pattern)
        static::creating(function ($model) {
            if (static::exists()) {
                throw new \Exception('Hanya boleh ada satu data identitas website.');
            }
        });

        // Clean up files when deleting
        static::deleting(function ($model) {
            if ($model->logo && Storage::disk('public')->exists($model->logo)) {
                Storage::disk('public')->delete($model->logo);
            }
            
            if ($model->favicon && Storage::disk('public')->exists($model->favicon)) {
                Storage::disk('public')->delete($model->favicon);
            }
        });

        // Clear theme cache when updating colors
        static::updated(function ($model) {
            $themeService = app(WebsiteThemeService::class);
            $themeService->clearCache();
        });
    }
}