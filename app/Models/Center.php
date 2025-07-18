<?php

namespace App\Models;

use Database\Factories\CenterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create(array $validated)
 * @method static pluck(string $string, string $string1)
 * @method static where(string $string, string $string1, mixed $id)
 * @property mixed $id
 */
class Center extends Model
{
    /** @use HasFactory<CenterFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];


    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }


    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }


    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
