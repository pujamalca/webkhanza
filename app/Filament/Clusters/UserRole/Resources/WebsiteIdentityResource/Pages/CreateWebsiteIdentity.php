<?php

namespace App\Filament\Clusters\UserRole\Resources\WebsiteIdentityResource\Pages;

use App\Filament\Clusters\UserRole\Resources\WebsiteIdentityResource;
use App\Models\WebsiteIdentity;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateWebsiteIdentity extends CreateRecord
{
    protected static string $resource = WebsiteIdentityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data identitas website berhasil dibuat!';
    }

    /**
     * Override mount untuk mencegah create jika data sudah ada
     */
    public function mount(): void
    {
        // Jika sudah ada data, redirect ke edit
        $existing = WebsiteIdentity::first();
        if ($existing) {
            Notification::make()
                ->title('Data sudah ada!')
                ->body('Hanya boleh ada satu data identitas website. Anda akan diarahkan ke halaman edit.')
                ->warning()
                ->send();
                
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $existing]));
        }

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan singleton constraint
        if (WebsiteIdentity::exists()) {
            throw new \Exception('Hanya boleh ada satu data identitas website.');
        }

        return $data;
    }
}