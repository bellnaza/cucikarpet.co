<?php

namespace App\Filament\Admin\Resources\EmailLogResource\Pages;

use App\Filament\Admin\Resources\EmailLogResource;
use Filament\Resources\Pages\ListRecords;

class ListEmailLogs extends ListRecords
{
    protected static string $resource = EmailLogResource::class;

    public function getSubheading(): ?string
    {
        return 'Emel dihantar melalui Resend oleh queue worker. Status "Gagal" biasanya bermaksud RESEND_API_KEY atau domain belum disahkan.';
    }
}
