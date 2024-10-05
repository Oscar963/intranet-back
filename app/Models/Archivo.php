<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;

    // Relación de un archivo con una página
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
