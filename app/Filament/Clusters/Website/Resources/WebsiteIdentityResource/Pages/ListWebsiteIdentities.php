<?php

namespace App\Filament\Clusters\Website\Resources\WebsiteIdentityResource\Pages;

use App\Filament\Clusters\Website\Resources\WebsiteIdentityResource;
use App\Models\WebsiteIdentity;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebsiteIdentities extends ListRecords
{
    protected static string $resource = WebsiteIdentityResource::class;

    public function mount(): void
    {
        // Redirect directly to edit page since there's only one website identity
        $websiteIdentity = WebsiteIdentity::first();
        
        if ($websiteIdentity) {
            // Redirect to edit page
            $this->redirect(WebsiteIdentityResource::getUrl('edit', ['record' => $websiteIdentity]));
        } else {
            // If no website identity exists, redirect to create page
            $this->redirect(WebsiteIdentityResource::getUrl('create'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}