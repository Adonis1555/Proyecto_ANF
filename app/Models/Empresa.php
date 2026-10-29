<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $primaryKey = 'id_empresa';
    public $timestamps = false;

    protected $fillable = ['nit', 'nombre_empresa', 'id_sector'];

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'id_sector');
    }
    
}
