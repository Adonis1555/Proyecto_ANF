<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValorRatio extends Model
{
    protected $table = 'valor_ratio';
    protected $primaryKey = 'id_ratio_valor';
    public $timestamps = false;

    protected $fillable = ['anio_ratio', 'valor', 'ratio_id_ratio', 'empresa_id_empresa'];

    public function ratio()
    {
        return $this->belongsTo(Ratio::class, 'ratio_id_ratio', 'id_ratio');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id_empresa', 'id_empresa');
    }
}
