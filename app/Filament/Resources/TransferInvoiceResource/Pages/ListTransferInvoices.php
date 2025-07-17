<?php

namespace App\Filament\Resources\TransferInvoiceResource\Pages;

use App\Filament\Resources\TransferInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransferInvoices extends ListRecords
{
    protected static string $resource = TransferInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
