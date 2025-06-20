<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entidad extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function nominas()
    {
        return $this->hasMany(Nomina::class);
    }
}
