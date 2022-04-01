<?php

namespace App\Http\Controllers;

use App\Models\capacitacion;
use App\Models\capacitacionTema;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class CapacitacionTemaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dato = capacitacionTema::where('estado','<>','2')->get();
        return $dato;
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
        if( $request->id > 0 ){
            $contenido = capacitacionTema::find($request->id);
        }else{
            $contenido = new capacitacionTema();
        }
        
        $contenido->tema = $request->tema;
        $contenido->capacitador = $request->capacitador;
        $contenido->area = $request->area;
        $contenido->nueva_area = $request->nueva_area;
        $contenido->estado = $request->estado;
        
        $contenido->save();        
        
        return $contenido;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\capacitacionTema  $capacitacionTema
     * @return \Illuminate\Http\Response
     */
    public function show(capacitacionTema $capacitacionTema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\capacitacionTema  $capacitacionTema
     * @return \Illuminate\Http\Response
     */
    public function edit(capacitacionTema $capacitacionTema)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\capacitacionTema  $capacitacionTema
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, capacitacionTema $capacitacionTema)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\capacitacionTema  $capacitacionTema
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dato = capacitacionTema::find($id);
        $dato->estado = 2;
        $dato->save();
        return $dato;
    }
    public function getAgendaCapacitaciones($id)
    {
        //agenda de capoacitaciones por periodo lectivo
        $dato = DB::table('capacitacion_agenda as cap')
        ->where('periodo_id','=',$id)
        ->leftjoin('capacitacion_temas as capte', 'cap.tema_id','=','capte.id')
        ->leftjoin('usuario as u','cap.id_usuario','=','u.idusuario')
        ->leftjoin('institucion as ist','cap.institucion_id','=','ist.idInstitucion')
        ->leftjoin('area as ar','capte.area','=','ar.idarea')
        ->select('cap.id','capte.tema','capte.capacitador', 'ar.nombrearea','capte.nueva_area','capte.estado',
        'ist.nombreInstitucion','u.nombres as asesorNombre','u.apellidos as asesorApellido','u.cedula', 'cap.id_usuario',
        'cap.nombre_institucion_temporal as institucionTemporal','cap.title','cap.label','cap.classes',
        'cap.startDate as fechaCapacitacion','cap.endDate as fechaFinCapacitacion','cap.hora_inicio','cap.hora_fin','cap.estado',
        'cap.personas','cap.observacion','cap.institucion_id_temporal as idInstitucionTemporal', 'cap.institucion_id')
        ->get();
        return $dato;
    }
}