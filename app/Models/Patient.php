<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static create(array $validated)
 */
class Patient extends Authenticatable
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
        'verified' => 'boolean',
    ];

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(Vital::class);
    }
}
