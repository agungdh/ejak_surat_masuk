<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    /** @use HasFactory<\Database\Factories\PegawaiFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function diklats(): HasMany
    {
        return $this->hasMany(Diklat::class);
    }

    public function ppms(): HasMany
    {
        return $this->hasMany(Ppm::class);
    }

    public function seminars(): HasMany
    {
        return $this->hasMany(Seminar::class);
    }

    public function webinars(): HasMany
    {
        return $this->hasMany(Webinar::class);
    }

    public function lcs(): HasMany
    {
        return $this->hasMany(Lc::class);
    }
}
