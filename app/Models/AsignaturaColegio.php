<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignaturaColegio extends Model
{
    use HasFactory;
    protected $table = "colegio_asignatura";
    protected $primaryKey = 'colegio_asignatura_id';
    protected $fillable=[
        'institucion_id',
        'asignatura_id',
        'estado',
        'idusuario',
        'libroweb',
        'libro_con_guia',
        'guia_didactica',
        'unidades',
        'ver',
        'agregar',
        'editar',
        'eliminar',
        'cuaderno_con_guia',
        'cuaderno_guia_didactica',
        'cuaderno_web',
        'planificacion_descargar',
        'planificacion_visualizar',
        'r_adicional_zona_diversion',
        'r_adicional_material',
        'r_adicional_propuestas',
        'r_adicional_adaptaciones',
        'r_adicional_articulos',
        'r_adicional_glosario',
        'r_adicional_simulador',
    
    ];
}
