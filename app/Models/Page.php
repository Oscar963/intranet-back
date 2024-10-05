<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $table = 'paginas';

    // Relación de una página con muchos archivos
    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }
}
