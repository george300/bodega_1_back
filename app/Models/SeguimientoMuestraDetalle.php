<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeguimientoMuestraDetalle extends Model
{
    use HasFactory;
    protected $table = "seguimiento_muestra_detalle";
    protected $primaryKey = 'id';
    protected $fillable = [
        'muestra_id',
        'libro_id',
        'cantidad',
        'cantidad_devolucion',
        'fecha_devolucion',
        'evidencia',
      
    ];
	public $timestamps = false;
}
