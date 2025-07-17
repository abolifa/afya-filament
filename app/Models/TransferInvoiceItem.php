<?php

namespace App\Models;

use Database\Factories\TransferInvoiceItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferInvoiceItem extends Model
{
    /** @use HasFactory<TransferInvoiceItemFactory> */
    use HasFactory;

    protected $fillable = [
        'transfer_invoice_id',
        'product_id',
        'quantity',
    ];


    public function transferInvoice(): BelongsTo
    {
        return $this->belongsTo(TransferInvoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
