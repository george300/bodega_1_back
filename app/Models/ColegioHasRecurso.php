<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColegioHasRecurso extends Model
{
    use HasFactory;
    protected $table = "colegios_has_recursos";
    protected $primaryKey = 'id';
    protected $fillable = [
     
        'colegio_asignatura_id',
        'institucion_id',
        'asignatura_id',
        'recurso_id',
        'permisos',
        'estado', 
    ];
}
