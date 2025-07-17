<?php

namespace App\Models;

use Database\Factories\VitalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vital extends Model
{
    /** @use HasFactory<VitalFactory> */
    use HasFactory;

    protected $guarded = ['id'];


    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
