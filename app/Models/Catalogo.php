<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    protected $table = 'catalogo';
    protected $primaryKey = 'id_catalogo';
    public $timestamps = false;

    protected $fillable = ['codigo_cuenta', 'nombre_cuenta', 'tipo_cuenta', 'empresa_id_empresa'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id_empresa', 'id_empresa');
    }
    public function reportesFinancieros()
    {
        return $this->hasMany(ReporteFinanciero::class, 'catalogo_id_catalogo', 'id_catalogo');
    }
}
