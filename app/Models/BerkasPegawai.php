<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BerkasPegawai extends Model
{
    protected $table = 'berkas_pegawai';
    
    // Tabel ini menggunakan composite key, kita set berkas sebagai primary key untuk Filament
    protected $primaryKey = 'berkas';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nik',
        'tgl_uploud',
        'tgl_berakhir',
        'kode_berkas',
        'berkas',
        'route_key',
    ];
    
    protected $casts = [
        'tgl_uploud' => 'date',
        'tgl_berakhir' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nik', 'nik');
    }

    public function masterBerkasPegawai()
    {
        return $this->belongsTo(MasterBerkasPegawai::class, 'kode_berkas', 'kode');
    }

    public function getRouteKeyName()
    {
        return 'route_key';
    }

    public function getRouteKey()
    {
        // Use route_key if available, otherwise generate from berkas filename
        if ($this->route_key) {
            return $this->route_key;
        }
        
        // Generate route_key if not exists
        // Use basename without extension and add prefix
        $filename = pathinfo($this->berkas, PATHINFO_FILENAME);
        $routeKey = 'bp_' . $filename;
        
        try {
            // Try raw SQL to bypass MariaDB prepared statement issues
            $escapedBerkas = addslashes($this->berkas);
            $escapedRouteKey = addslashes($routeKey);
            \DB::unprepared("UPDATE berkas_pegawai SET route_key = '{$escapedRouteKey}' WHERE berkas = '{$escapedBerkas}'");
            
            // Update the current model instance
            $this->route_key = $routeKey;
            
        } catch (\Exception $e) {
            // Handle errors gracefully
            \Log::info('Error updating route_key for berkas_pegawai (raw SQL failed):', [
                'berkas' => $this->berkas,
                'route_key' => $routeKey,
                'error' => $e->getMessage(),
                'note' => 'Returning generated route_key without saving to DB'
            ]);
            
            // Return the generated route_key without saving to database
            return $routeKey;
        }
        
        return $routeKey;
    }
    
    public function resolveRouteBinding($value, $field = null)
    {
        $fieldName = $field ?? $this->getRouteKeyName();
        
        // Try to find by route_key first
        $record = $this->where($fieldName, $value)->first();
        
        if (!$record && $fieldName === 'route_key') {
            // If route_key lookup failed, try to find by generating from berkas
            // Format: bp_filename -> find record with berkas containing filename
            if (str_starts_with($value, 'bp_')) {
                $filename = substr($value, 3);
                $record = $this->where('berkas', 'LIKE', '%' . $filename . '%')->first();
                
                if ($record) {
                    // Try to update route_key for future use using raw SQL
                    try {
                        $escapedBerkas = addslashes($record->berkas);
                        $escapedRouteKey = addslashes($value);
                        \DB::unprepared("UPDATE berkas_pegawai SET route_key = '{$escapedRouteKey}' WHERE berkas = '{$escapedBerkas}'");
                        $record->route_key = $value; // Update model instance
                    } catch (\Exception $e) {
                        // Log the error but continue
                        \Log::info('Could not update route_key for berkas_pegawai during route binding (raw SQL):', [
                            'berkas' => $record->berkas,
                            'route_key' => $value,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
        
        return $record;
    }
}