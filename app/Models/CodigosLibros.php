<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CodigosLibros extends Model 
{
    
    protected $table = "codigoslibros";
    protected $primaryKey = 'codigo';
    protected $fillable=[
        'serie','libro','anio','contrato','idusuario','idusuario_creador_codigo','libro_idlibro','estado','fecha_create','id_periodo','created_at','updated_at',
        'estado_liquidacion',
        'verif1',
        'verif2',
        'verif3',
        'verif4',
        'verif5',
        'verif6',
        'verif7',
        'verif8',
        'verif9',
        'verif10',
        'bc_estado',
        'bc_fecha_ingreso',
        'bc_periodo',
        'bc_institucion'
    ];
}
