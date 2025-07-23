<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diklat extends Model
{
    use HasFactory;

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisPelatihan(): BelongsTo
    {
        return $this->belongsTo(JenisPelatihan::class);
    }
}
