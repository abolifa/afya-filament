<?php

namespace App\Models;

use Database\Factories\TransferInvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferInvoice extends Model
{
    /** @use HasFactory<TransferInvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'from_center_id',
        'to_center_id',
        'status',
    ];

    public function fromCenter(): BelongsTo
    {
        return $this->belongsTo(Center::class, 'from_center_id');
    }

    public function toCenter(): BelongsTo
    {
        return $this->belongsTo(Center::class, 'to_center_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransferInvoiceItem::class);
    }
}
