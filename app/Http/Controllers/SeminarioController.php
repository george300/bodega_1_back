<?php

namespace App\Http\Controllers;

use App\Models\Seminario;
use App\Models\SeminarioEncuesta;
use App\Models\SeminarioHasUsuario;
use App\Models\Seminarios;
use Illuminate\Http\Request;
use DB;

class SeminarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //contar los webinars registrados asistentes
        if($request->contar){
            $registrados = DB::SELECT("SELECT count(*) as registrados  from seminario_has_usuario
             WHERE seminario_id = '$request->seminario_id'
             ");
            $asistentes = DB::SELECT("SELECT count(*) as asistentes  from seminario_has_usuario
                 WHERE seminario_id = '$request->seminario_id'
                 AND asistencia = '1'
                 ");
            $total_encuestas = DB::SELECT("SELECT DISTINCT s.* FROM seminario_has_usuario  s
            LEFT JOIN seminario_respuestas r ON s.seminario_id = r.id_seminario
            WHERE s.asistencia = '1'
            AND s.seminario_id = '$request->seminario_id'
            AND s.usuario_id = r.id_usuario
               ");

             return [
                 "registrados" => $registrados,
                 "asistentes" => $asistentes,
                 "encuestas" => count($total_encuestas),

             ];
        }else{
            $seminario = DB::SELECT("SELECT * FROM seminario WHERE estado = '1' order by fecha_inicio desc;");
            return $seminario;
        }

    }

    public function buscarSeminario(Request $request){
        $curso = DB::SELECT("SELECT * FROM seminario WHERE idcurso = ?",[$request->idcurso]);
        $registrados = DB::SELECT("SELECT COUNT(*) as registrados FROM inscripcion join seminario on seminario.idseminario = inscripcion.seminario_idseminario WHERE seminario.idcurso = ?",[$request->idcurso]);
        $data = [
            'curso' => $curso,
            'total' => $registrados,
        ];
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }
    public function eliminarSeminario(Request $request)
    {
        DB::UPDATE("UPDATE `seminario`
        SET
        `estado` = '0'
        WHERE `idseminario` = ? ;",[$request->idcurso]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->idseminario){
            $seminario = new Seminario();
            $id = uniqid();
            $seminario->nombre = $request->nombre;
            $seminario->descripcion = $request->descripcion;
            $seminario->fecha_inicio = $request->fecha_inicio;
            $seminario->hora_inicio = $request->hora_inicio;
            $seminario->link_presentacion = $request->link_presentacion;
            $seminario->cantidad_participantes = (int) $request->cantidad_particiantes;
            $seminario->link_registro = "https://prolipadigital.com.ec/inscripciones/public/?curso=".$id;
            $seminario->idcurso = $id;
            $seminario->tiempo_curso = $request->tiempo_curso;
            $seminario->save();
        }else{
            $seminario = Seminario::find($request->idseminario);
            $seminario->nombre = $request->nombre;
            $seminario->descripcion = $request->descripcion;
            $seminario->fecha_inicio = $request->fecha_inicio;
            $seminario->hora_inicio = $request->hora_inicio;
            $seminario->link_presentacion = $request->link_presentacion;
            $seminario->cantidad_participantes = (int) $request->cantidad_particiantes;
            $seminario->tiempo_curso = $request->tiempo_curso;
            $seminario->save();
        }


        return $seminario;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\seminario  $seminario
     * @return \Illuminate\Http\Response
     */
    public function show($seminario)
    {
        $seminario = DB::SELECT("SELECT ec.cedula, s.*
        FROM encuestas_certificados ec, seminario s
        WHERE s.estado = '1'
        and ec.id_seminario = s.idseminario
        and ec.cedula = $seminario
        order by fecha_inicio desc;");
        return $seminario;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\seminario  $seminario
     * @return \Illuminate\Http\Response
     */
    public function edit(seminario $seminario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\seminario  $seminario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, seminario $seminario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\seminario  $seminario
     * @return \Illuminate\Http\Response
     */
    public function destroy(seminario $seminario)
    {
        return $seminario;
    }

    public function encuesta_certificados($id){
        $encuesta = DB::SELECT("SELECT ec.*, u.nombres, u.apellidos, u.email, u.telefono, i.nombreInstitucion, g.deskripsi as grupo
        FROM encuestas_certificados ec, usuario u, institucion i, sys_group_users g
        WHERE id_seminario = $id
        and ec.cedula = u.cedula
        and u.id_group = g.id
        and u.institucion_idInstitucion = i.idInstitucion");
        return $encuesta;
    }

    public function seminariosDocente($id)
    {
        $seminarios = DB::SELECT("SELECT s.*, i.* FROM seminario s, inscripcion i WHERE s.idseminario = i.seminario_idseminario AND i.cedula LIKE '$id' ORDER BY s.fecha_inicio DESC");
        return $seminarios;
    }


    ///SEMINARIOS V2
    public function get_seminarios($id_periodo){

        $seminarios = DB::SELECT("SELECT s.*, i.nombreInstitucion AS nombre_institucion, c.nombre AS nombre_ciudad,
        COUNT(sr.id_seminario) AS cant_respuestas FROM seminarios s
         LEFT JOIN institucion i ON s.id_institucion = i.idInstitucion
         LEFT JOIN ciudad c ON i.ciudad_id = c.idciudad
         LEFT JOIN seminario_respuestas sr ON s.id_seminario = sr.id_seminario
         WHERE s.estado = 1 AND s.periodo_id = $id_periodo
         GROUP BY s.id_seminario
         ORDER BY s.id_seminario DESC
         "
         );
        return $seminarios;
    }

    //para traer los webinars
    public function obtenerWebinars(){
        $todate  = date('Y-m-d');
        // return $todate;
        $webinars = DB::SELECT("SELECT s.*, CONCAT(s.descripcion, ' - ' ,s.nombre) as webinar, i.nombreInstitucion AS nombre_institucion, c.nombre AS nombre_ciudad, COUNT(sr.id_seminario) AS cant_respuestas
        FROM seminarios s
        LEFT  JOIN institucion i ON s.id_institucion = i.idInstitucion
        LEFT JOIN ciudad c ON i.ciudad_id = c.idciudad
         LEFT JOIN seminario_respuestas sr ON s.id_seminario = sr.id_seminario
         WHERE s.estado = 1
         AND  s.tipo_webinar = '1'
         AND  s.fecha_fin > '$todate'
         GROUP BY s.id_seminario
         ORDER BY s.id_seminario DESC
         ");
         return  $webinars;
    }

    public function sumarEncuestasDescargadas(Request $request){

        $certificados = DB::SELECT("SELECT * FROM seminario_has_usuario
        WHERE seminario_id ='$request->seminario_id'
        AND usuario_id = '$request->usuario_id'
        ");

        if(count($certificados) == 0){

        }else{
            $extraerContador = $certificados[0]->certificado_cont;
            $extraerId = $certificados[0]->seminario_has_usuario_id;

            $certificado =  SeminarioHasUsuario::findOrFail($extraerId);
            $certificado->certificado_cont = $extraerContador + 1;
            $certificado->save();

        }



    }

    public function resumenWebinar($periodo){
        $webinars = DB::SELECT("SELECT s.*, CONCAT(s.descripcion, ' - ' ,s.nombre) as webinar, i.nombreInstitucion AS nombre_institucion, c.nombre AS nombre_ciudad, COUNT(sr.id_seminario) AS cant_respuestas
        FROM seminarios s
        LEFT  JOIN institucion i ON s.id_institucion = i.idInstitucion
        LEFT JOIN ciudad c ON i.ciudad_id = c.idciudad
        LEFT JOIN seminario_respuestas sr ON s.id_seminario = sr.id_seminario
        INNER JOIN periodoescolar_has_institucion pi on i.idInstitucion = pi.institucion_idInstitucion
        INNER JOIN periodoescolar p ON pi.periodoescolar_idperiodoescolar = p.idperiodoescolar
        WHERE s.estado = 1
        AND s.tipo_webinar = '1'
        AND p.estado = '1'
        AND p.idperiodoescolar = $periodo
        GROUP BY s.id_seminario");

        $datos = [];
        $data = array();
        $arr_respuestas = array();
        foreach($webinars as $key => $item){

            $registrados = DB::SELECT("SELECT * from seminario_has_usuario
            WHERE seminario_id = '$item->id_seminario'
            ");

            $total_encuestas = DB::SELECT("SELECT DISTINCT s.* FROM seminario_has_usuario  s
                LEFT JOIN seminario_respuestas r ON s.seminario_id = r.id_seminario
                WHERE s.asistencia = '1'
                AND s.seminario_id = '$item->id_seminario'
                AND s.usuario_id = r.id_usuario
            ");

            $respuestas_encuestas = DB::SELECT("SELECT * FROM `seminario_respuestas` WHERE `id_seminario` = $item->id_seminario");
            // $respuestas_encuestas_1 = json_decode($respuestas_encuestas[0]->respuestas);
            // return response()->json(array('response' => $item->id_seminario));

            $val_preg_7 = 0;
            foreach($respuestas_encuestas as $keyr => $value){

                $respuestas_json = json_decode($value->respuestas);
                foreach($respuestas_json as $key1 => $value1){
                    $cont = 0;
                    foreach($value1 as $key2 => $value2){
                        $cont++; if( $cont == 7 ){ $val_preg_7 += $value2; }
                    }
                }
            }

            $totr_resp_7 = 1;
            if( count($respuestas_encuestas) != 0 ){
                $totr_resp_7 = count($respuestas_encuestas);
            }

            $val_preg_7 = $val_preg_7/$totr_resp_7;

            $asistentes = DB::SELECT("SELECT *  from seminario_has_usuario
                WHERE seminario_id = '$item->id_seminario'
                AND asistencia = '1'
            ");

            $datos[$key] = [
                "seminario_id" => $item->id_seminario,
                "seminario" => $item->nombre,
                "descripcion" => $item->descripcion,
                "capacitador" => $item->capacitador,
                "val_preg_7" => floatval($val_preg_7),
                "registrados" => count($registrados) ,
                "asistentes" => count($asistentes),
                "encuestas_llenadas" => count($total_encuestas),
            ];

        }

        $informacion = [
            "seminarios" => $datos,
        ];
        return $informacion;

    }

    public function get_seminarios_docente($id){
        $seminarios = DB::SELECT("SELECT s.*, sr.respuestas FROM seminarios s LEFT JOIN usuario u ON s.id_institucion = u.institucion_idInstitucion LEFT JOIN seminario_respuestas sr ON s.id_seminario = sr.id_seminario AND sr.id_usuario = $id WHERE u.idusuario = $id AND s.estado = 1 AND s.tipo_webinar = 0 GROUP BY s.id_seminario ORDER BY s.id_seminario DESC");
        return $seminarios;
    }

    public function obtener_seminarios_docente(Request $request){
        $seminarios = DB::SELECT("SELECT s.* FROM seminarios s
        WHERE s.id_institucion = '$request->institucion_id'
        AND s.tipo_webinar = '0'
        AND s.estado = '1'
        ORDER BY s.id_seminario DESC

        ");

        $datos = [];
        $data = [];
        foreach($seminarios as $key => $item){
            $asistencia = DB::SELECT("SELECT asistencia FROM seminario_has_usuario
            WHERE usuario_id = '$request->idusuario'
            AND seminario_id = '$item->id_seminario'
            ");
            $respuestas = DB::SELECT("SELECT * FROM seminario_respuestas
            WHERE id_seminario = '$item->id_seminario'
            AND id_usuario = '$request->idusuario'
            ORDER BY id_respuesta DESC

            ");

            //PARA LA ASISTENCIA
            if(count($asistencia) ==  0){
                $Rasistencia = 0;
            }

            if(count($asistencia) > 0){
                $Rasistencia = $asistencia[0]->asistencia;
            }else{
                $Rasistencia = 0;
            }

            //PARA LA RESPUESTA
            if(count($respuestas) ==  0){
                $Rrespuestas = 0;
            }

            if(count($respuestas) > 0){
                $Rrespuestas = $respuestas[0];
            }else{
                $Rrespuestas = 0;
            }
            $datos[$key] = [
                "id_seminario" => $item->id_seminario,
                "nombre" => $item->nombre,
                "descripcion" => $item->descripcion,
                "fecha_inicio" => $item->fecha_inicio,
                "fecha_fin" => $item->fecha_fin,
                "link_reunion" => $item->link_reunion,
                "id_institucion" => $item->id_institucion,
                "estado" => $item->estado,
                "capacitador" => $item->capacitador,
                "cant_asistentes" => $item->cant_asistentes,
                "asistencia_activa" => $item->asistencia_activa,
                "tipo_webinar" => $item->tipo_webinar,
                "asistencia" => $Rasistencia,
                "respuestas" => $Rrespuestas
            ];

        }

        return $datos;
    }

    public function obtener_webinars_docente(Request $request){

        $seminarios = DB::SELECT("SELECT s.*, sr.respuestas FROM seminarios s
        LEFT JOIN seminario_respuestas sr ON s.id_seminario = sr.id_seminario
        AND sr.id_usuario = '$request->idusuario'
        WHERE s.tipo_webinar = 1
        AND s.estado = 1
        GROUP BY s.id_seminario");

        $datos = [];
        $data = [];
        foreach($seminarios as $key => $item){
            $asistencia = DB::SELECT("SELECT asistencia FROM seminario_has_usuario
            WHERE usuario_id = '$request->idusuario'
            AND seminario_id = '$item->id_seminario'
            ");


            //PARA LA ASISTENCIA
            if(count($asistencia) ==  0){
                $Rasistencia = 0;
            }

            if(count($asistencia) > 0){
                $Rasistencia = $asistencia[0]->asistencia;
            }else{
                $Rasistencia = 0;
            }


            $datos[$key] = [
                "id_seminario" => $item->id_seminario,
                "nombre" => $item->nombre,
                "descripcion" => $item->descripcion,
                "fecha_inicio" => $item->fecha_inicio,
                "fecha_fin" => $item->fecha_fin,
                "link_reunion" => $item->link_reunion,
                "id_institucion" => $item->id_institucion,
                "estado" => $item->estado,
                "capacitador" => $item->capacitador,
                "cant_asistentes" => $item->cant_asistentes,
                "asistencia_activa" => $item->asistencia_activa,
                "tipo_webinar" => $item->tipo_webinar,
                "asistencia" => $Rasistencia,
                "respuestas" => $item->respuestas
            ];

        }

        return $datos;
    }


    public function get_seminarios_webinar($id){
        $seminarios = DB::SELECT("SELECT s.*, sr.respuestas FROM seminarios s
        LEFT JOIN seminario_respuestas sr ON s.id_seminario = sr.id_seminario
        AND sr.id_usuario = $id
        WHERE s.tipo_webinar = 1
        AND s.estado = 1
        GROUP BY s.id_seminario");
        return $seminarios;
    }


    public function get_webinars(Request $request){

        //verificar si hay encuestas
        $encuestas = DB::SELECT("SELECT * FROM seminario_respuestas r
        WHERE r.id_usuario = '$request->idusuario'
        ");
        //si hay encuestas pero no estan registrados
        if(count($encuestas) < 0){

            $webinars = DB::SELECT("SELECT DISTINCT sm.seminario_has_usuario_id,sm.asistencia, sm.usuario_id, s.* , sr.respuestas
                FROM seminario_has_usuario sm
                LEFT JOIN seminarios s ON sm.seminario_id = s.id_seminario
                LEFT JOIN seminario_respuestas sr ON s.id_seminario = sr.id_seminario
                AND sr.id_usuario = '$request->idusuario' WHERE sm.usuario_id = '$request->idusuario'
                AND s.estado = 1
                AND s.tipo_webinar = 1
                ORDER BY sm.seminario_has_usuario_id DESC
                ");
                return $webinars;

        }else{
            //SI TODO ESTA BIEN
            $usuario = DB::SELECT("SELECT * FROM usuario where idusuario = '$request->idusuario'");
            $cedula = $usuario[0]->cedula;
            $institucion = $usuario[0]->institucion_idInstitucion;


            foreach($encuestas as $key => $item){

                $registroEncuesta = DB::SELECT("SELECT * FROM seminario_has_usuario u
                WHERE u.usuario_id = '$request->idusuario'
                AND u.seminario_id = '$item->id_seminario'
                ");



                if(count($registroEncuesta) == 0){
                    $seminario =  new  SeminarioHasUsuario();
                    $seminario->usuario_id =  $item->id_usuario;
                    $seminario->cedula =      $cedula;
                    $seminario->seminario_id = $item->id_seminario;
                    $seminario->institucion_id = $institucion;
                    $seminario->asistencia = "1";
                    $seminario->save();
                }


            }

            $webinars = DB::SELECT("SELECT DISTINCT sm.seminario_has_usuario_id,sm.asistencia, sm.usuario_id, s.* , sr.respuestas
            FROM seminario_has_usuario sm
            LEFT JOIN seminarios s ON sm.seminario_id = s.id_seminario
            LEFT JOIN seminario_respuestas sr ON s.id_seminario = sr.id_seminario
            AND sr.id_usuario = '$request->idusuario' WHERE sm.usuario_id = '$request->idusuario'
            AND s.estado = 1
            AND s.tipo_webinar = 1
            ORDER BY sm.seminario_has_usuario_id DESC
            ");
            return $webinars;

        }







        // if(count($encuestas) > 0){
        //     return "hola";



        // }else{
        //         //verificar si existe los registros de los seminarios de las encuestas
        //         $registroEncuesta = DB::SELECT("SELECT * FROM seminario_has_usuario u
        //         WHERE u.usuario_id = '$request->idusuario'
        //         AND u.seminario_id = '41'
        //         ");

        //         return $registroEncuesta;
        // }


    }

    public function webinarAsistencia(Request $request){
        $seminario =  SeminarioHasUsuario::findOrFail($request->seminario_has_usuario_id);
        $seminario->asistencia = "1";
        $seminario->save();

        if($seminario){
            return ["status" =>"1","message" => "Asistencia registrada correctamente"];
        }else{
            return ["status" =>"0","message" => "No se pudo registrar la asistencia"];
        }

    }

    public function SeminarioAsistencia(Request $request)
    {

        $BuscarUsuarioSeminario = DB::SELECT("SELECT s.* FROM seminario_has_usuario s
        WHERE cedula = '$request->cedula'
        AND seminario_id = '$request->seminario_id'
        ");

        if(count($BuscarUsuarioSeminario) >0){

            $idAsistencia = $BuscarUsuarioSeminario[0]->seminario_has_usuario_id;
            $seminario =  SeminarioHasUsuario::findOrFail($idAsistencia);
            $seminario->asistencia = "1";
            $seminario->save();
            if($seminario){
                return ["status" =>"1","message" => "Asistencia registrada correctamente"];
            }else{
                return ["status" =>"0","message" => "No se pudo registrar la asistencia"];
            }
        }else{

            $seminario =  new  SeminarioHasUsuario();
            $seminario->usuario_id =  $request->usuario_id;
            $seminario->cedula =      $request->cedula;
            $seminario->seminario_id = $request->seminario_id;
            $seminario->institucion_id = $request->institucion_id;
            $seminario->asistencia = "1";
            $seminario->save();

            if($seminario){
                return ["status" =>"1","message" => "Asistencia registrada correctamente"];
            }else{
                return ["status" =>"0","message" => "No se pudo registrar la asistencia"];
            }
        }


    }

    public function get_instituciones(){
        $instituciones = DB::SELECT("SELECT DISTINCT i.idInstitucion AS id_institucion, CONCAT(i.nombreInstitucion, ' - ', c.nombre) AS nombre_institucion, p.idperiodoescolar, p.estado FROM institucion i, periodoescolar_has_institucion phi, periodoescolar p, ciudad c WHERE i.idInstitucion = phi.institucion_idInstitucion AND i.ciudad_id = c.idciudad AND phi.periodoescolar_idperiodoescolar = p.idperiodoescolar AND p.estado = '1' AND i.estado_idEstado = 1 ORDER BY c.nombre");
        return $instituciones;
    }

    public function guardar_seminario(Request $request){
        if( $request->id_seminario ){
            DB::UPDATE("UPDATE `seminarios` SET `nombre`=?,`descripcion`=?,`fecha_inicio`=?,`fecha_fin`=?,`id_institucion`=?, `link_reunion`=?,`capacitador`=?,`cant_asistentes`=?,`asistencia_activa`=?,`tipo_webinar`=?,`link_recurso`=?,`clave_recurso`=? WHERE `id_seminario` = ?", [$request->nombre,$request->descripcion,$request->fecha_inicio,$request->fecha_fin,$request->id_institucion,$request->link_reunion,$request->capacitador,$request->cant_asistentes,$request->asistencia_activa,$request->tipo_webinar,$request->link_recurso,$request->clave_recurso,$request->id_seminario]);
        }else{
            DB::INSERT("INSERT INTO `seminarios`(`nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `link_reunion`, `id_institucion`, `capacitador`, `cant_asistentes`, `asistencia_activa`, `tipo_webinar`,`periodo_id`, `link_recurso`,`clave_recurso`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", [$request->nombre,$request->descripcion,$request->fecha_inicio,$request->fecha_fin,$request->link_reunion,$request->id_institucion,$request->capacitador,$request->cant_asistentes,$request->asistencia_activa,$request->tipo_webinar,$request->periodo_id,$request->link_recurso,$request->clave_recurso]);
        }

    }

    public function get_preguntas_seminario(){


        $preguntas = DB::SELECT("SELECT p.*, s.nombre_seccion FROM seminario_preguntas p, seminario_secciones s WHERE p.estado = 1 AND p.seccion_pregunta = s.id_seccion ORDER BY p.id_pregunta");

        return $preguntas;
    }

    public function save_encuesta(Request $request){
        // return $request->respuestas;
        DB::INSERT("INSERT INTO `seminario_respuestas`(`id_seminario`, `id_usuario`, `respuestas`) VALUES (?,?,?)", [$request->id_seminario, $request->id_usuario, $request->respuestas]);

    }
    public function eliminar_seminario($id_seminario){
        DB::UPDATE("UPDATE `seminarios` SET `estado` = 0 WHERE `id_seminario` = $id_seminario");
    }

    public function asistentes_seminario($id_seminario){
        $asistentes = DB::SELECT("SELECT u.nombres, u.apellidos, u.email, u.cedula, i.nombreInstitucion, s.asistencia , g.deskripsi as rol
        FROM seminario_has_usuario s, usuario u, institucion i,  sys_group_users g
         WHERE s.seminario_id = $id_seminario
         AND s.usuario_id = u.idusuario
         AND u.id_group = g.id
         AND u.institucion_idInstitucion = i.idInstitucion");

        return $asistentes;
    }

    public function reporte_seminario($id){

        $seminario = DB::SELECT("SELECT * FROM seminario_respuestas sr WHERE sr.id_seminario = $id");

        $data = array();
        $arr_respuestas = array();

        $i = 0;
        foreach ($seminario as $key => $value) {
            $arr_resp = json_decode($value->respuestas);
            foreach ($arr_resp as $key_1 => $value_1) {

                foreach ($value_1 as $key_2 => $value_2) {
                    $data_pregunta = DB::SELECT("SELECT * FROM `seminario_preguntas` WHERE `id_pregunta` = $key_2");
                    $data['items'][$i] = [
                        "tipo_pregunta" => $data_pregunta[0]->tipo_pregunta,
                        "id_pregunta" => $key_2,
                        "nombre_pregunta" => $data_pregunta[0]->nombre_pregunta,
                        "respuesta" => $value_2
                    ];
                    $i++;
                }

            }
        }

        return $data;

    }


    public function get_periodos_seminarios(){
        $periodos = DB::SELECT("SELECT * FROM `periodoescolar`");
        return $periodos;
    }

    public function actualiza_periodo_seminario(){
        $periodos = DB::SELECT("SELECT * FROM `seminarios`");
        foreach($periodos as $key => $value){

            $periodo_inst = DB::SELECT("SELECT pi.periodoescolar_idperiodoescolar FROM institucion i, periodoescolar_has_institucion pi, periodoescolar p WHERE i.idInstitucion = pi.institucion_idInstitucion AND pi.periodoescolar_idperiodoescolar = p.idperiodoescolar AND p.estado = '1' and i.idinstitucion = ?", [$value->id_institucion]);
            if($periodo_inst){
                DB::UPDATE("UPDATE seminarios s SET s.periodo_id = ? WHERE s.id_seminario = ?", [$periodo_inst[0]->periodoescolar_idperiodoescolar, $value->id_seminario]);
            }

        }
    }

    public function editar_codigos_masivos(){
        set_time_limit(6000000);
        ini_set('max_execution_time', 6000000);
        $cont = ''; $cant = 0;
        $codigos = [];


        for( $i=0; $i<count($codigos); $i++ ){
            $edicion = DB::UPDATE("UPDATE `codigoslibros` SET `idusuario` = 60608, `id_periodo` = 16 WHERE `codigo` = ?  AND (`idusuario` = 0 OR `idusuario` IS NULL OR `idusuario` = '')", [$codigos[$i]]);
            if( $edicion ){ $cont .= ($codigos[$i].'_');  $cant++;}
        }

        return '*****CODIGOS EDITADOS: ' . $cont . '*****CANTIDAD: ' . $cant;

    }

}
