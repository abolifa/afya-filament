<?php

namespace App\Models;

use Database\Factories\StockMovementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @method static create(array $array)
 */
class StockMovement extends Model
{
    /** @use HasFactory<StockMovementFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'actor_type', 'actor_id',
        'subject_type', 'subject_id',
        'from_center_id', 'to_center_id',
        'patient_id', 'supplier_id',
    ];

    // line items
    public function items(): HasMany
    {
        return $this->hasMany(StockMovementItem::class);
    }

    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function fromCenter(): BelongsTo
    {
        return $this->belongsTo(Center::class, 'from_center_id');
    }

    public function toCenter(): BelongsTo
    {
        return $this->belongsTo(Center::class, 'to_center_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
