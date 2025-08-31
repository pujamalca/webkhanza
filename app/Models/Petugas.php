<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $table = 'petugas';
    
    protected $primaryKey = 'nip';
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nip',
        'route_key',
        'nama',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'gol_darah',
        'agama',
        'stts_nikah',
        'alamat',
        'kd_jbtn',
        'no_telp',
        'email',
        'status',
    ];
    
    protected $casts = [
        'tgl_lahir' => 'date',
        'status' => 'string',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'kd_jbtn', 'kd_jbtn');
    }

    public function getRouteKeyName()
    {
        return 'route_key';
    }

    public function getRouteKey()
    {
        // Use route_key if available, otherwise generate from nip
        if ($this->route_key) {
            return $this->route_key;
        }
        
        // Generate route_key if not exists
        $routeKey = 'pg_' . str_replace('/', '_', $this->nip);
        
        try {
            // Try raw SQL to bypass MariaDB prepared statement issues
            $escapedNip = addslashes($this->nip);
            $escapedRouteKey = addslashes($routeKey);
            \DB::unprepared("UPDATE petugas SET route_key = '{$escapedRouteKey}' WHERE nip = '{$escapedNip}'");
            
            // Update the current model instance
            $this->route_key = $routeKey;
            
        } catch (\Exception $e) {
            // Handle MariaDB errors gracefully
            \Log::info('Error updating route_key for petugas (raw SQL failed):', [
                'nip' => $this->nip,
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
            // If route_key lookup failed, try to extract nip from route_key
            // Format: pg_12_09_1988_001 -> 12/09/1988/001
            if (str_starts_with($value, 'pg_')) {
                $nip = str_replace('_', '/', substr($value, 3));
                $record = $this->where('nip', $nip)->first();
                
                if ($record) {
                    // Try to update route_key for future use using raw SQL
                    try {
                        $escapedNip = addslashes($nip);
                        $escapedRouteKey = addslashes($value);
                        \DB::unprepared("UPDATE petugas SET route_key = '{$escapedRouteKey}' WHERE nip = '{$escapedNip}'");
                        $record->route_key = $value; // Update model instance
                    } catch (\Exception $e) {
                        // Log the error but continue
                        \Log::info('Could not update route_key for petugas during route binding (raw SQL):', [
                            'nip' => $nip,
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
}