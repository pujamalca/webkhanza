<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Dokter extends Model
{
    use HasFactory, LogsActivity;
    
    protected $table = 'dokter';
    
    protected $primaryKey = 'kd_dokter';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'kd_dokter',
        'route_key',
        'nm_dokter',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'gol_drh',
        'agama',
        'almt_tgl',
        'no_telp',
        'email',
        'stts_nikah',
        'kd_sps',
        'alumni',
        'no_ijn_praktek',
        'status',
    ];
    
    protected $casts = [
        'tgl_lahir' => 'date',
        'status' => 'string',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'kd_dokter', 'nik');
    }

    public function spesialis()
    {
        return $this->belongsTo(Spesialis::class, 'kd_sps', 'kd_sps');
    }

    public function getRouteKeyName()
    {
        return 'route_key';
    }

    public function getRouteKey()
    {
        // Use route_key if available, otherwise generate from kd_dokter
        if ($this->route_key) {
            return $this->route_key;
        }
        
        // Generate route_key if not exists
        $routeKey = 'dr_' . str_replace('/', '_', $this->kd_dokter);
        
        try {
            // Try raw SQL to bypass MariaDB prepared statement issues
            $escapedKdDokter = addslashes($this->kd_dokter);
            $escapedRouteKey = addslashes($routeKey);
            \DB::unprepared("UPDATE dokter SET route_key = '{$escapedRouteKey}' WHERE kd_dokter = '{$escapedKdDokter}'");
            
            // Update the current model instance
            $this->route_key = $routeKey;
            
        } catch (\Exception $e) {
            // Handle MariaDB errors gracefully
            \Log::info('Error updating route_key for dokter (raw SQL failed):', [
                'kd_dokter' => $this->kd_dokter,
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
            // If route_key lookup failed, try to extract kd_dokter from route_key
            // Format: dr_12_09_1988_001 -> 12/09/1988/001
            if (str_starts_with($value, 'dr_')) {
                $kdDokter = str_replace('_', '/', substr($value, 3));
                $record = $this->where('kd_dokter', $kdDokter)->first();
                
                if ($record) {
                    // Try to update route_key for future use using raw SQL
                    try {
                        $escapedKdDokter = addslashes($kdDokter);
                        $escapedRouteKey = addslashes($value);
                        \DB::unprepared("UPDATE dokter SET route_key = '{$escapedRouteKey}' WHERE kd_dokter = '{$escapedKdDokter}'");
                        $record->route_key = $value; // Update model instance
                    } catch (\Exception $e) {
                        // Log the error but continue
                        \Log::info('Could not update route_key for dokter during route binding (raw SQL):', [
                            'kd_dokter' => $kdDokter,
                            'route_key' => $value,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
        
        return $record;
    }

    public function getEnumValues($column)
    {
        $instance = new static;
        $type = \DB::select(\DB::raw("SHOW COLUMNS FROM {$instance->getTable()} WHERE Field = '{$column}'"))[0]->Type;
        preg_match_all("/'([^']+)'/", $type, $matches);
        return $matches[1];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['kd_dokter', 'nm_dokter', 'jk', 'kd_sps', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('dokter');
    }
}