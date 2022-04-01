<?php

namespace App\Http\Controllers;

use App\Models\actividad_animacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActividadAnimacionController extends Controller
{
    /**
     * Display a listing of the resource.,
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $act = DB::SELECT("SELECT * FROM actividades_animaciones");
        return $act;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\actividad_animacion  $actividad_animacion
     * @return \Illuminate\Http\Response
     */
    public function getAsignaturas()
    {
        $asignatura = DB::SELECT("SELECT * FROM asignatura WHERE estado = '1' and tipo_asignatura = '1'");
        return $asignatura;

    }
    // Obtener una asignatura para los PROYECTOS del strapi
    public function asignaturaIdProyectos($id)
    {
        $asignatura = DB::SELECT("SELECT * FROM asignatura WHERE idasignatura = $id ");
        return $asignatura;

    }
   
    public function temasUnidad(Request $request)
    {
        $temas = DB::SELECT("SELECT t.nombre_tema, t.id AS id, t.nombre_tema AS label, t.id_asignatura, t.unidad, a.nombreasignatura, t.clasificacion FROM temas t, asignatura a WHERE t.id_asignatura = a.idasignatura AND t.unidad = $request->unidad AND t.id_asignatura = $request->asignatura AND t.estado=1 ORDER BY  t.nombre_tema + 0 ASC");

        return $temas;
    }

    
    public function temasUnidadID($id)
    {
        $temas = DB::SELECT("SELECT * FROM temas t WHERE t.id_unidad = $id ORDER BY t.nombre_tema + 0 ASC");

        return $temas;
    }


    public function actividades_x_Tema($id)
    {
        $temas = DB::SELECT("SELECT a.*, t.id as idtema, t.nombre_tema, asig.idasignatura, asig.nombreasignatura, t.id_unidad, ul.id_unidad_libro, ul.id_libro, ul.unidad, ul.nombre_unidad, u.nombres, u.apellidos,  lib.weblibro
        FROM actividades_animaciones a, temas t, asignatura asig, unidades_libros ul, usuario u, libro lib 
        WHERE t.id = a.id_tema
        and lib.asignatura_idasignatura = asig.idasignatura
        and t.id_unidad = ul.id_unidad_libro 
        and a.id_usuario = u.idusuario 
        and t.id_asignatura = asig.idasignatura 
        and a.id_tema = $id");

        return $temas;
    }
    public function actividades_x_Libro($id){
        $actividades= DB::SELECT("SELECT t.nombre_tema, a.*, u.nombres, u.apellidos, ul.nombre_unidad, ul.unidad, lib.weblibro
        FROM temas t, actividades_animaciones a, usuario u, unidades_libros ul, libro lib
        WHERE t.id_asignatura = $id
        and t.id_asignatura = lib.asignatura_idasignatura
        and a.id_tema = t.id 
        and a.id_usuario = u.idusuario 
        and ul.id_unidad_libro = t.id_unidad");
        return $actividades;
    }
    public function actividadesBuscarFechas($fecha)
    {
        $buscar = DB::SELECT("SELECT a.*, t.nombre_tema,  asig.idasignatura, asig.nombreasignatura, t.id_unidad, ul.id_unidad_libro, ul.id_libro, ul.unidad, ul.nombre_unidad, u.nombres, u.apellidos, lib.weblibro 
        FROM actividades_animaciones a, temas t, asignatura asig, unidades_libros ul, usuario u, libro lib 
        WHERE t.id = a.id_tema
        and lib.asignatura_idasignatura = asig.idasignatura
        and t.id_unidad = ul.id_unidad_libro 
        and a.id_usuario = u.idusuario 
        and t.id_asignatura = asig.idasignatura 
        and a.created_at LIKE '$fecha%' 
        ORDER BY a.id_item DESC");

        return $buscar;
    }

    public function actividades_libros_unidad($id_unidad)
    {
        $actividades = DB::SELECT('SELECT * FROM actividades_animaciones aa, temas t WHERE aa.id_tema = t.id AND aa.tipo = 0 AND t.id_unidad = ?',[$id_unidad]);

        return $actividades;
    }

    public function animaciones_libros_unidad($id_unidad)
    {
        $animaciones = DB::SELECT('SELECT * FROM actividades_animaciones aa, temas t WHERE aa.id_tema = t.id AND aa.tipo = 1 AND t.id_unidad = ?',[$id_unidad]);

        return $animaciones;
    }

    ////desglose temas
    public function actividades_libros_unidad_tema($id_tema)
    {
        $actividades = DB::SELECT('SELECT * FROM actividades_animaciones aa, temas t WHERE aa.id_tema = t.id AND aa.tipo = 0 AND t.id = ?',[$id_tema]);

        return $actividades;
    }

    public function animaciones_libros_unidad_tema($id_tema)
    {
        $animaciones = DB::SELECT('SELECT * FROM actividades_animaciones aa, temas t WHERE aa.id_tema = t.id AND aa.tipo = 1 AND t.id = ?',[$id_tema]);

        return $animaciones;
    }
    
    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if( $request->id_item ){
            $actividad = actividad_animacion::find($request->id_item);
        }else{
            $actividad = new actividad_animacion();
        }

        $actividad->id_usuario = $request->id_usuario;
        $actividad->id_tema = $request->id_tema;
        $actividad->tipo = $request->tipo;
        $actividad->link = $request->link;
        $actividad->page = $request->page;
        
        $actividad->save();

        return $actividad;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\actividad_animacion  $actividad_animacion
     * @return \Illuminate\Http\Response
     */
    public function show(actividad_animacion $actividad_animacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\actividad_animacion  $actividad_animacion
     * @return \Illuminate\Http\Response
     */
    public function edit(actividad_animacion $actividad_animacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\actividad_animacion  $actividad_animacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, actividad_animacion $actividad_animacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\actividad_animacion  $actividad_animacion
     * @return \Illuminate\Http\Response
     */
    
    public function destroy(actividad_animacion $actividad_animacion)
    {

    }
    public function eliminaActividad($id_item)
    {
        $actividad = actividad_animacion::find($id_item);
        $actividad->delete();
    }
    public function carpetaActividades($id)
    {
        $actividad = DB::SELECT("SELECT weblibro from libro WHERE asignatura_idasignatura = $id");

        return $actividad;
    }
}
