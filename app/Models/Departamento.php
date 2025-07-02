<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';
    protected $fillable = [
        'code',
        'name',
    ];

    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }
}
