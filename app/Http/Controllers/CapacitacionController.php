<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Capacitacion;
use DB;

class CapacitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $capacitacion = DB::SELECT("SELECT c.id,
        c.tema_id,
        c.label,
        c.title,
        c.classes,date_format(c.endDate, '%Y-%m-%d %H:%i:%s') as endDate ,
        c.startDate,
        c.hora_inicio,
        c.hora_fin,
        c.institucion_id,
        c.periodo_id,
        c.id_usuario,
        c.estado,
        c.observacion,
        c.institucion_id_temporal,
        c.nombre_institucion_temporal,
        c.estado_institucion_temporal,
        c.personas,
        p.idperiodoescolar, p.periodoescolar AS periodo, i.nombreInstitucion FROM capacitacion_agenda c
        LEFT JOIN periodoescolar p ON c.periodo_id = p.idperiodoescolar
        LEFT JOIN institucion i ON  c.institucion_id = i.idInstitucion
        WHERE c.id_usuario = '$request->id_usuario'
       
        ");
        return $capacitacion;
    }
    public function temasCapacitacion(Request $request){
        if($request->validarPorFecha){
            $validar = DB::SELECT("SELECT * FROM capacitacion_agenda a
            WHERE a.endDate = '$request->fecha'
            AND tema_id  ='$request->tema_id'
            AND estado = '1'
            ");
            return $validar;
        }else{
            $temas = DB::SELECT("SELECT c.*   FROM capacitacion_temas c
            WHERE c.estado = '1'
            ");
            return $temas;
        }
    
    }
    public function store(Request $request)
    {
        //para editar la capacitacion agenda
        if( $request->id != 0 ){
            $agenda = Capacitacion::find($request->id);
        //para guardar la capacitacion agenda  
        }else{
            $agenda = new Capacitacion();
        } 
        //si crean una insitucion temporal
        if($request->estado_institucion_temporal == 1 ){
            $agenda->periodo_id = $request->periodo_id_temporal;
            $agenda->institucion_id_temporal = $request->institucion_id_temporal;
            $agenda->nombre_institucion_temporal = $request->nombreInstitucion;
            $agenda->institucion_id = "";
        }
        if($request->estado_institucion_temporal == 0){
            $agenda->institucion_id = $request->institucion_id;
            $agenda->institucion_id_temporal = "";
            $agenda->nombre_institucion_temporal = "";
              //para traer el periodo
              $buscarPeriodo = $this->traerPeriodo($request->institucion_id);
              if($buscarPeriodo["status"] == "1"){
                  $obtenerPeriodo = $buscarPeriodo["periodo"][0]->periodo;
                  $agenda->periodo_id = $obtenerPeriodo;
              }
        }
      
        $agenda->id_usuario = $request->idusuario;
        $agenda->title = $request->title;
        $agenda->label = $request->label;
        $agenda->classes = $request->classes;
        $agenda->startDate = $request->endDate;
        $agenda->endDate = $request->endDate;
        if($request->observacion == "null"){
            $agenda->observacion = "";    
        }else{
            $agenda->observacion = $request->observacion;   
        }
        $agenda->hora_inicio = $request->hora_inicio;
        $agenda->hora_fin = $request->hora_fin;
        $agenda->tema_id = $request->tema_id;
        $agenda->estado_institucion_temporal =$request->estado_institucion_temporal;
        $agenda->save();
        return 
        $agenda;
    }
    public function traerPeriodo($institucion_id){
        $periodoInstitucion = DB::SELECT("SELECT idperiodoescolar AS periodo , periodoescolar AS descripcion FROM periodoescolar WHERE idperiodoescolar = ( 
            SELECT  pir.periodoescolar_idperiodoescolar as id_periodo
            from institucion i,  periodoescolar_has_institucion pir         
            WHERE i.idInstitucion = pir.institucion_idInstitucion
            AND pir.id = (SELECT MAX(phi.id) AS periodo_maximo FROM periodoescolar_has_institucion phi
            WHERE phi.institucion_idInstitucion = i.idInstitucion
            AND i.idInstitucion = '$institucion_id'))
        ");
        if(count($periodoInstitucion)>0){
            return ["status" => "1", "message"=>"correcto","periodo" => $periodoInstitucion];
        }else{
            return ["status" => "0", "message"=>"no hay periodo"];
        }
    }
    public function delete_agenda_asesor($id_agenda)
    {
        DB::DELETE("DELETE FROM `capacitacion_agenda` WHERE `id` = $id_agenda");
    }
    public function edit_agenda_admin(Request $request)
    {
        $agenda = Capacitacion::find($request->id);
        $agenda->personas =$request->personas;
        $agenda->observacion =$request->observacion;
        $agenda->estado =$request->estado;
        $agenda->save();
    }
}
