<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuKontrol extends Model
{
    /** @use HasFactory<\Database\Factories\KartuKontrolFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tanggal_pelaksanaan' => 'date',
        ];
    }
}
