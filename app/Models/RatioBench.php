<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatioBench extends Model
{
    protected $table = 'ratio_bench';
    protected $primaryKey = 'id_bench';
    public $timestamps = false;

    protected $fillable = ['valor_bech', 'ratio_id_ratio', 'sector_id_sector'];

    public function ratio()
    {
        return $this->belongsTo(ValorRatio::class, 'ratio_id_ratio');
    }
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id_sector');
    }
}
