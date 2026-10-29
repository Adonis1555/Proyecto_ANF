<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ratio extends Model
{
    protected $table = 'ratio';
    protected $primaryKey = 'id_ratio';
    public $timestamps = false;

    protected $fillable = ['nombre_ratio'];

    public function valorRatios()
    {
        return $this->hasMany(ValorRatio::class, 'ratio_id_ratio', 'id_ratio');
    }
}
