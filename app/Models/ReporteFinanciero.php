<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteFinanciero extends Model
{
    protected $table = 'reporte_financiero';
    protected $primaryKey = 'id_reporte';
    public $timestamps = false;

    protected $fillable = ['anio', 'balance', 'saldo', 'catalogo_id_catalogo'];

    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id_catalogo');
    }
}
