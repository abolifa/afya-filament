<?php

namespace App\Models;

use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 * @method static where(string $string, mixed $centerId)
 */
class Doctor extends Authenticatable
{
    /** @use HasFactory<DoctorFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];


    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

}
