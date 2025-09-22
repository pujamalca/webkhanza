.+<?php

namespace App\Filament\Clusters\Administrator\Resources\ActivityLogResource\Pages;

use App\Filament\Clusters\Administrator\Resources\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cleanup')
                ->label('Cleanup Old Logs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cleanup Old Activity Logs')
                ->modalDescription('Are you sure you want to delete activity logs older than 30 days? This action cannot be undone.')
                ->action(function () {
                    $deletedCount = \Spatie\Activitylog\Models\Activity::where('created_at', '<', now()->subDays(30))->delete();
                    
                    Notification::make()
                        ->title('Success')
                        ->body("Successfully deleted {$deletedCount} old activity logs.")
                        ->success()
                        ->send();
                })
                ->visible(fn(): bool => auth()->user()->can('system_settings_access')),
        ];
    }

    public function getTitle(): string
    {
        return 'Activity Logs';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Could add statistics widgets here if needed
        ];
    }
}