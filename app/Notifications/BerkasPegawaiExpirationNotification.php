<?php

namespace App\Notifications;

use App\Models\BerkasPegawai;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class BerkasPegawaiExpirationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $expiring1Month,
        public int $expiring3Months, 
        public int $expiring6Months
    ) {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'expiring_1_month' => $this->expiring1Month,
            'expiring_3_months' => $this->expiring3Months,
            'expiring_6_months' => $this->expiring6Months,
        ];
    }
    
    public static function sendToCurrentUser()
    {
        $summary = BerkasPegawai::getExpirationSummary();
        
        if ($summary['expiring_1_month'] > 0 || $summary['expiring_3_months'] > 0 || $summary['expiring_6_months'] > 0) {
            $message = [];
            
            if ($summary['expiring_1_month'] > 0) {
                $message[] = "{$summary['expiring_1_month']} dokumen akan expires dalam 1 bulan";
            }
            if ($summary['expiring_3_months'] > 0) {
                $message[] = "{$summary['expiring_3_months']} dokumen akan expires dalam 3 bulan";
            }
            if ($summary['expiring_6_months'] > 0) {
                $message[] = "{$summary['expiring_6_months']} dokumen akan expires dalam 6 bulan";
            }
            
            FilamentNotification::make()
                ->title('Peringatan Berkas Pegawai')
                ->body(implode(' • ', $message))
                ->warning()
                ->send();
        }
    }
}