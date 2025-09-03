<?php

namespace App\Filament\Clusters\UserRole\Resources\WebsiteIdentityResource\Pages;

use App\Filament\Clusters\UserRole\Resources\WebsiteIdentityResource;
use App\Models\WebsiteIdentity;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebsiteIdentities extends ListRecords
{
    protected static string $resource = WebsiteIdentityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Data Identitas')
                ->visible(fn() => WebsiteIdentity::count() === 0),
        ];
    }

    /**
     * Override untuk redirect ke edit jika data sudah ada (singleton behavior)
     */
    public function mount(): void
    {
        parent::mount();
        
        // Jika sudah ada data, redirect ke halaman edit
        $identity = WebsiteIdentity::first();
        if ($identity) {
            $this->redirect(WebsiteIdentityResource::getUrl('edit', ['record' => $identity]));
        }
    }

    protected function getHeaderTitle(): string
    {
        return 'Identitas Website';
    }

    protected function getHeaderDescription(): ?string
    {
        return 'Kelola informasi identitas dan branding website Anda.';
    }
}