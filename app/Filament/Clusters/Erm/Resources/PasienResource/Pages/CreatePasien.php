<?php

namespace App\Filament\Clusters\Erm\Resources\PasienResource\Pages;

use App\Filament\Clusters\Erm\Resources\PasienResource;
use App\Models\Pasien;
use Filament\Resources\Pages\CreateRecord;

class CreatePasien extends CreateRecord
{
    protected static string $resource = PasienResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove auto_generate_rm from data since it's not a database field
        unset($data['auto_generate_rm']);
        
        // Auto-generate RM if not provided
        if (empty($data['no_rkm_medis'])) {
            $lastRM = Pasien::max('no_rkm_medis');
            if ($lastRM) {
                if (is_numeric($lastRM)) {
                    $newRM = str_pad((int)$lastRM + 1, 6, '0', STR_PAD_LEFT);
                } else {
                    preg_match('/(\d+)/', $lastRM, $matches);
                    $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
                    $newRM = str_pad($number, 6, '0', STR_PAD_LEFT);
                }
            } else {
                $newRM = '000001';
            }
            $data['no_rkm_medis'] = $newRM;
        }
        
        return $data;
    }

    protected function getFormData(): array
    {
        $data = parent::getFormData();
        
        // Set default auto-generate to true and generate RM
        $data['auto_generate_rm'] = true;
        
        if (empty($data['no_rkm_medis'])) {
            $lastRM = Pasien::max('no_rkm_medis');
            if ($lastRM) {
                if (is_numeric($lastRM)) {
                    $newRM = str_pad((int)$lastRM + 1, 6, '0', STR_PAD_LEFT);
                } else {
                    preg_match('/(\d+)/', $lastRM, $matches);
                    $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
                    $newRM = str_pad($number, 6, '0', STR_PAD_LEFT);
                }
            } else {
                $newRM = '000001';
            }
            $data['no_rkm_medis'] = $newRM;
        }
        
        return $data;
    }
}