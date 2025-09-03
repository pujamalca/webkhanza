<?php

namespace App\Filament\Clusters\UserRole\Resources\WebsiteIdentityResource\Pages;

use App\Filament\Clusters\UserRole\Resources\WebsiteIdentityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditWebsiteIdentity extends EditRecord
{
    protected static string $resource = WebsiteIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url('/', shouldOpenInNewTab: true),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Identitas website berhasil diperbarui!';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle file cleanup untuk logo
        if (isset($data['logo']) && $data['logo'] !== $this->record->logo) {
            // Hapus logo lama jika ada
            if ($this->record->logo && Storage::disk('public')->exists($this->record->logo)) {
                Storage::disk('public')->delete($this->record->logo);
            }
        }

        // Handle file cleanup untuk favicon
        if (isset($data['favicon']) && $data['favicon'] !== $this->record->favicon) {
            // Hapus favicon lama jika ada
            if ($this->record->favicon && Storage::disk('public')->exists($this->record->favicon)) {
                Storage::disk('public')->delete($this->record->favicon);
            }
        }

        return $data;
    }

    protected function getHeaderTitle(): string
    {
        return 'Edit Identitas Website';
    }

    protected function getHeaderDescription(): ?string
    {
        return 'Perbarui informasi identitas dan branding website Anda.';
    }
}