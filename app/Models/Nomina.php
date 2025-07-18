<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nominas';
    protected $fillable = [
        'name',
    ];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
