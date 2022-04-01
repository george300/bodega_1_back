<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\SallePreguntas;
use App\Models\SalleEvaluaciones;
use Illuminate\Provider\Image;
use Illuminate\Support\Facades\File;


class SallePreguntasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $preguntas = DB::SELECT("SELECT p . *, t.nombre_tipo, t.indicaciones, t.descripcion_tipo, a.nombre_asignatura, a.cant_preguntas, a.estado as estado_asignatura, ar.nombre_area, ar.id_area, ar.estado as estado_area FROM salle_preguntas p, tipos_preguntas t, salle_asignaturas a, salle_areas ar WHERE p.id_tipo_pregunta = t.id_tipo_pregunta AND p.id_asignatura = a.id_asignatura AND a.id_area = ar.id_area AND a.estado = 1 AND ar.estado = 1 AND p.estado = 1");

        if(!empty($preguntas)){
            foreach ($preguntas as $key => $value) {
                $opciones = DB::SELECT("SELECT * FROM `salle_opciones_preguntas` WHERE `id_pregunta` = ?",[$value->id_pregunta]);
                
                $data['items'][$key] = [
                    'pregunta' => $value,
                    'opciones' => $opciones,
                ];            
            }
        }else{
            $data = [];
        }
        return $data;
    
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
        $ruta = public_path('img/salle/img_preguntas');

        if( $request->id_pregunta >0 ){
            $pregunta = SallePreguntas::find($request->id_pregunta);
            if($request->file('img_pregunta') && $request->file('img_pregunta') != null && $request->file('img_pregunta')!= 'null'){
                $file = $request->file('img_pregunta');
                $fileName = uniqid().$file->getClientOriginalName();
                $file->move($ruta,$fileName);
                if( file_exists('img/salle/img_preguntas/'.$request->img_pregunta_old) && $request->img_pregunta_old != '' ){
                    unlink('img/salle/img_preguntas/'.$request->img_pregunta_old);
                }
            }else{
                $fileName = $request->img_pregunta_old;
            }
        }else{
            $pregunta = new SallePreguntas();
            if($request->file('img_pregunta')){
                $file = $request->file('img_pregunta');
                $ruta = public_path('img/salle/img_preguntas');
                $fileName = uniqid().$file->getClientOriginalName();
                $file->move($ruta,$fileName);
            }else{
                $fileName = '';
            }
        }

        $pregunta->id_tipo_pregunta  = $request->id_tipo_pregunta ;
        $pregunta->id_asignatura  = $request->id_asignatura ;
        $pregunta->descripcion = $request->descripcion;
        $pregunta->img_pregunta = $fileName;
        $pregunta->puntaje_pregunta = $request->puntaje_pregunta;
        $pregunta->estado = 1;
        $pregunta->editor = $request->editor;
        
        $pregunta->save();

        return $pregunta;
    }



    public function cargar_opcion_salle(Request $request)
    {   
        $ruta = public_path('img/salle/img_preguntas');

        if($request->file('img_opcion')){
            $file = $request->file('img_opcion');
            $ruta = public_path('img/salle/img_preguntas');
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($ruta,$fileName);
        }else{
            $fileName = '';
        }

        $opcion = DB::INSERT("INSERT INTO `salle_opciones_preguntas`(`id_pregunta`, `opcion`, `img_opcion`, `tipo`, `cant_coincidencias`) VALUES ($request->id_pregunta, '$request->opcion', '$fileName', $request->tipo, $request->cant_coincidencias)");
        
        $opciones = DB::SELECT("SELECT * FROM salle_opciones_preguntas WHERE id_pregunta = $request->id_pregunta ORDER BY created_at");
        
        return $opciones;
    }


    public function editar_opcion_salle(Request $request)
    {   
        $ruta = public_path('img/salle/img_preguntas');

        if($request->file('img_opcion') && $request->file('img_opcion') != null && $request->file('img_opcion')!= 'null'){
            $file = $request->file('img_opcion');
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($ruta,$fileName);
            if( file_exists('img/salle/img_preguntas/'.$request->img_opcion_old) && $request->img_pregunta_old != '' ){
                unlink('img/salle/img_preguntas/'.$request->img_opcion_old);
            }
        }else{
            $fileName = $request->img_opcion_old;
        }

        $opcion = DB::UPDATE("UPDATE `salle_opciones_preguntas` SET `opcion`='$request->opcion',`img_opcion`='$fileName',`tipo`=$request->tipo,`cant_coincidencias`=$request->cant_coincidencias WHERE `id_opcion_pregunta`= $request->id_opcion_pregunta");
        
        $opciones = DB::SELECT("SELECT * FROM salle_opciones_preguntas WHERE id_pregunta = $request->id_pregunta ORDER BY created_at");
        
        return $opciones;
        
    }

    public function quitar_opcion_salle($id)
    {
        $opciones = DB::DELETE("DELETE FROM salle_opciones_preguntas WHERE id_opcion_pregunta = $id");
    }

    public function eliminar_pregunta_salle($id)
    {
        $pregunta = DB::UPDATE("UPDATE `salle_preguntas` SET `estado` = 0 WHERE `id_pregunta` = $id");

        return $pregunta;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $preguntas = DB::SELECT("SELECT p . *, t.nombre_tipo, t.indicaciones, t.descripcion_tipo, a.nombre_asignatura, a.cant_preguntas, a.estado as estado_asignatura, ar.nombre_area, ar.id_area, ar.estado as estado_area FROM salle_preguntas p, tipos_preguntas t, salle_asignaturas a, salle_areas ar WHERE p.id_tipo_pregunta = t.id_tipo_pregunta AND p.id_asignatura = a.id_asignatura AND a.id_area = ar.id_area AND a.estado = 1 AND ar.estado = 1 AND p.estado = 1 AND p.id_asignatura = $id");

        if(!empty($preguntas)){
            foreach ($preguntas as $key => $value) {
                $opciones = DB::SELECT("SELECT * FROM `salle_opciones_preguntas` WHERE `id_pregunta` = ?",[$value->id_pregunta]);
                
                $data['items'][$key] = [
                    'pregunta' => $value,
                    'opciones' => $opciones,
                ];            
            }
        }else{
            $data = [];
        }
        return $data;
    }

    public function opciones_pregunta_salle($id)
    {
        $opciones = DB::SELECT("SELECT p . *, t.nombre_tipo, t.indicaciones, t.descripcion_tipo FROM salle_preguntas p, tipos_preguntas t WHERE p.id_tipo_pregunta = t.id_tipo_pregunta");
         
        return $opciones;
    }


    public function cargar_opcion_vf_salle(Request $request)
    {
        if( $request->id_opcion ){
            $opcion = DB::DELETE("DELETE FROM `salle_opciones_preguntas` WHERE id_pregunta = $request->id_pregunta");
        }
            
        $opcion = DB::INSERT("INSERT INTO `salle_opciones_preguntas`(`id_pregunta`, `opcion`, `img_opcion`, `tipo`, `cant_coincidencias`) VALUES ($request->id_pregunta, '$request->opcion', '', $request->tipo, $request->cant_coincidencias)");

        if( $request->opcion == 'Verdadero' || $request->opcion == 'Si' ){
            if( $request->opcion == 'Verdadero' ){
                $nombre_opcion = 'Falso';
            }else{
                $nombre_opcion = 'No';
            }
            $opcion = DB::INSERT("INSERT INTO `salle_opciones_preguntas`(`id_pregunta`, `opcion`, `img_opcion`, `tipo`, `cant_coincidencias`) VALUES ($request->id_pregunta, '$nombre_opcion', '', 0, $request->cant_coincidencias)");
        }else{
            if( $request->opcion == 'Falso' ){
                $nombre_opcion = 'Verdadero';
            }else{
                $nombre_opcion = 'Si';
            }
            $opcion = DB::INSERT("INSERT INTO `salle_opciones_preguntas`(`id_pregunta`, `opcion`, `img_opcion`, `tipo`, `cant_coincidencias`) VALUES ($request->id_pregunta, '$nombre_opcion', '', 0, $request->cant_coincidencias)");
        }
        
        $opciones = DB::SELECT("SELECT * FROM salle_opciones_preguntas WHERE id_pregunta = $request->id_pregunta ORDER BY created_at");
        
        return $opciones;
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /*********************************************************************************
    ************** EVALUACIONES SALLE
    ********************************************************************************/
    public function salle_getConfiguracion($institucion)
    {
        $configuracion = DB::SELECT("SELECT i.nombreInstitucion, i.direccionInstitucion, c.fecha_inicio, c.fecha_fin, c.cant_evaluaciones, c.ver_respuestas, c.observaciones FROM institucion i, salle_configuracion c WHERE i.id_configuracion = c.id_configuracion AND i.idInstitucion = $institucion");

        $fecha_actual = date("Y-m-d G:i:s");
        $horario_permitido = 0;
        // return $fecha_actual .'<'. $configuracion[0]->fecha_fin .'&&'. $fecha_actual .'>'. $configuracion[0]->fecha_inicio;
        if( $fecha_actual < $configuracion[0]->fecha_fin && $fecha_actual > $configuracion[0]->fecha_inicio ){
            $horario_permitido = 1;
        }

        return response()->json(['configuracion' => $configuracion, 'horario_permitido' => $horario_permitido]);
    }

    public function salle_finalizarEvaluacion(Request $request)
    {   // estado 2 = evaluacion finalizada
        DB::UPDATE("UPDATE `salle_evaluaciones` SET `estado` = 2 WHERE `id_evaluacion` = ? AND `id_usuario` = ?",[$request->id_evaluacion, $request->id_usuario]);
    }

    public function evaluaciones_resueltas_salle($docente)
    {   
        // para mostrar lisatdo de evaluaciones resuletas en el perfil del docente
        $evaluaciones = DB::SELECT("SELECT * FROM salle_evaluaciones se WHERE se.id_usuario = ? AND se.estado = 2",[$docente]);
        return $evaluaciones;
    }
    
    public function generar_evaluacion_salle($id_docente, $id_institucion)
    {  
        set_time_limit(60000);
        ini_set('max_execution_time', 60000);

        $fecha_actual = date("Y-m-d G:i:s");
        $periodo = date('Y');
        $configuracion = DB::SELECT("SELECT sc . * FROM institucion i, salle_configuracion sc WHERE i.idInstitucion = $id_institucion AND i.id_configuracion = sc.id_configuracion AND i.estado_idEstado = 1");

        if( !empty($configuracion) ){
            if( $fecha_actual < $configuracion[0]->fecha_fin && $fecha_actual > $configuracion[0]->fecha_inicio ){
                // evaluaciones del docente que no esten eliminadas !=3, y que corresponda al periodo actual
                $eval_doc = DB::SELECT("SELECT * FROM `salle_evaluaciones` WHERE `id_usuario` = $id_docente AND `estado` != 3 AND created_at LIKE '$periodo%'");
                if( count($eval_doc) < $configuracion[0]->cant_evaluaciones ){
                    
                    $evaluacion = new SalleEvaluaciones();
                    $evaluacion->id_usuario = $id_docente;
                    $evaluacion->save();
                    $id_evaluacion = $evaluacion->id_evaluacion;
    
                    $asignaturas = DB::SELECT("SELECT sa.id_asignatura, sa.id_area, sa.cant_preguntas FROM salle_asignaturas_has_docente sd, salle_asignaturas sa WHERE sd.id_docente = $id_docente AND sd.id_asignatura = sa.id_asignatura AND sa.estado = 1");
    
                    foreach ($asignaturas as $key => $value_asignaturas) {
                        $preguntas = DB::SELECT("SELECT sp.id_pregunta FROM salle_preguntas sp WHERE sp.id_asignatura = $value_asignaturas->id_asignatura AND sp.estado = 1 ORDER BY RAND() LIMIT $value_asignaturas->cant_preguntas");
    
                        foreach ($preguntas as $key => $value_preguntas) {
                            DB::INSERT("INSERT INTO `salle_preguntas_evaluacion`(`id_evaluacion`, `id_pregunta`) VALUES (?,?)",[$id_evaluacion, $value_preguntas->id_pregunta]);
                        }
                    }
                    
                    // se cargan asiganturas basicas a la evaluacion
                    $asignaturas_basicas = DB::SELECT("SELECT sa.id_asignatura, sa.id_area, sa.cant_preguntas FROM salle_asignaturas sa, salle_areas sar WHERE sa.id_area = sar.id_area AND sar.id_area = 1 AND sa.estado = 1 AND sar.estado = 1");
                    foreach ($asignaturas_basicas as $key => $value_basicas) {
                        $preguntas = DB::SELECT("SELECT sp.id_pregunta FROM salle_preguntas sp WHERE sp.id_asignatura = $value_basicas->id_asignatura AND sp.estado = 1 ORDER BY RAND() LIMIT $value_basicas->cant_preguntas");
    
                        foreach ($preguntas as $key => $value_preguntas) {
                            DB::INSERT("INSERT INTO `salle_preguntas_evaluacion`(`id_evaluacion`, `id_pregunta`) VALUES (?,?)",[$id_evaluacion, $value_preguntas->id_pregunta]);
                        }
                    }
                    return $this->obtener_evaluacion_salle($id_evaluacion, $id_docente);
                }else{
                    if( $eval_doc[0]->estado === 1 ){
                        return $this->obtener_evaluacion_salle($eval_doc[0]->id_evaluacion, $id_docente);
                    }else{
                        return 2; // esta evaluacion ya fue completada
                    }
                }
            }else{
                return 0; // Horario no permitido
            }
        }else{
            return 1; // no existe configuracion para su institución
        }
        
    }



    public function obtener_evaluacion_salle($id_evaluacion, $id_docente){
        set_time_limit(600000);
        ini_set('max_execution_time', 600000);
        $i_area = 0;
        $data = array();
        $data_asignaturas = array();
        $data_preguntas = array();
        $areas = DB::SELECT("SELECT DISTINCT sar.id_area, sar.nombre_area, sar.descripcion_area, sar.descripcion_area, spe.id_evaluacion FROM salle_preguntas_evaluacion spe, salle_preguntas sp, salle_asignaturas sa, salle_areas sar WHERE spe.id_pregunta = sp.id_pregunta AND sp.id_asignatura = sa.id_asignatura AND sa.id_area = sar.id_area AND spe.id_evaluacion = $id_evaluacion ORDER BY `sar`.`id_area` ASC;");
        foreach ($areas as $key_areas => $value_areas) {
            $asignaturas = DB::SELECT("SELECT DISTINCT sa . * FROM salle_evaluaciones se, salle_preguntas_evaluacion spe, salle_preguntas sp, salle_asignaturas sa WHERE se.id_evaluacion = ? AND se.id_evaluacion = spe.id_evaluacion AND spe.id_pregunta = sp.id_pregunta AND sa.id_asignatura = sa.id_asignatura AND sa.id_area = ?",[$id_evaluacion, $value_areas->id_area]);
            
            foreach ($asignaturas as $key_asignaturas => $value_asignaturas) {
                $preguntas = DB::SELECT("SELECT sp . *, t.nombre_tipo, t.indicaciones, t.descripcion_tipo FROM salle_preguntas_evaluacion pe, salle_preguntas sp, tipos_preguntas t WHERE sp.id_tipo_pregunta = t.id_tipo_pregunta AND pe.id_evaluacion = ? AND pe.id_pregunta = sp.id_pregunta AND sp.id_asignatura = ? AND sp.estado = 1",[$id_evaluacion, $value_asignaturas->id_asignatura]);
                
                foreach ($preguntas as $key_preguntas => $value_preguntas) {
                    $opciones = DB::SELECT("SELECT * FROM `salle_opciones_preguntas` WHERE `id_pregunta` = ?",[$value_preguntas->id_pregunta]);
                    $respuestas = DB::SELECT("SELECT * FROM `salle_respuestas_preguntas` WHERE `id_pregunta` = ? AND id_usuario = ? AND `id_evaluacion` = ?",[$value_preguntas->id_pregunta, $id_docente, $id_evaluacion]);
                    
                    $data_preguntas['preguntas'][$key_preguntas] = ['pregunta' => $value_preguntas, 'opciones' => $opciones, 'respuestas' => $respuestas];

                    $data_asignaturas['asignaturas'][$key_asignaturas] = ['asignatura' => $value_asignaturas, $data_preguntas];
                }
                $data['areas'][$key_areas] = [
                    'area' => $value_areas,
                    $data_asignaturas
                ];
            }
            $data_preguntas = [];
            $data_asignaturas = [];

        }
        
        return $data;
    }

    public function salle_guardarSeleccion(Request $request){
        
        if( $request->tipo_pregunta == 1 ){
            // pregunta opcion multiple
            $opcion = DB::SELECT("SELECT * FROM `salle_respuestas_preguntas` WHERE `id_evaluacion` = $request->id_evaluacion AND `id_pregunta` = $request->id_pregunta AND `respuesta` = $request->id_opcion_pregunta AND `id_usuario` = $request->id_usuario");

            if( count($opcion) > 0 ){
                DB::DELETE("DELETE FROM `salle_respuestas_preguntas` WHERE `id_respuesta_pregunta` = ?", [$opcion[0]->id_respuesta_pregunta]);
            }else{
                DB::INSERT("INSERT INTO `salle_respuestas_preguntas`(`id_evaluacion`, `id_pregunta`, `id_usuario`, `respuesta`, `puntaje`) VALUES (?, ?, ?, ?, ?)", [$request->id_evaluacion, $request->id_pregunta, $request->id_usuario, $request->id_opcion_pregunta, $request->puntaje_seleccion]);
            }
        }else{
            DB::DELETE("DELETE FROM `salle_respuestas_preguntas` WHERE `id_evaluacion` = ? AND `id_pregunta` = ? AND `id_usuario` = ?", [$request->id_evaluacion, $request->id_pregunta, $request->id_usuario]);
            
            DB::INSERT("INSERT INTO `salle_respuestas_preguntas`(`id_evaluacion`, `id_pregunta`, `id_usuario`, `respuesta`, `puntaje`) VALUES (?, ?, ?, ?, ?)", [$request->id_evaluacion, $request->id_pregunta, $request->id_usuario, $request->id_opcion_pregunta, $request->puntaje_seleccion]);
        }

        return $request;
    }


    public function salle_sincronizar_preguntas($asignatura1, $asignatura2, $usuario){

        set_time_limit(60000);
        ini_set('max_execution_time', 60000);
        // se consulta las preguntas de la asignatura que se desea copiar
        $preguntas = DB::SELECT("SELECT * FROM `salle_preguntas` WHERE `id_asignatura` = $asignatura1");
        
        // se eliminan las preguntas de la asigantura a cargar las nuevas
        DB::DELETE("DELETE FROM `salle_preguntas` WHERE `id_asignatura` = $asignatura2");
        

        if(!empty($preguntas)){
            foreach ($preguntas as $key => $value) {
                
                $preg_sync = new SallePreguntas();
                $preg_sync->id_asignatura = $asignatura2;
                $preg_sync->id_tipo_pregunta = $value->id_tipo_pregunta;
                $preg_sync->descripcion = $value->descripcion;
                $preg_sync->img_pregunta = $value->img_pregunta;
                $preg_sync->puntaje_pregunta = $value->puntaje_pregunta;
                $preg_sync->estado = $value->estado;
                $preg_sync->editor = $usuario;
                $preg_sync->save();

                // return $preg_sync->id_pregunta;

                // se consulta las opciones de la asignatura que se desea copiar
                $opciones = DB::SELECT("SELECT * FROM `salle_opciones_preguntas` WHERE `id_pregunta` = $value->id_pregunta");
                
                foreach ($opciones as $keyO => $valueO) {
                    DB::INSERT("INSERT INTO `salle_opciones_preguntas`(`id_pregunta`, `opcion`, `img_opcion`, `tipo`, `cant_coincidencias`) VALUES (?,?,?,?,?)",[$preg_sync->id_pregunta, $valueO->opcion, $valueO->img_opcion, $valueO->tipo, $valueO->cant_coincidencias]);
                }

            }
            return "1"; // sincronizacion correcta
        }else{
            return "0"; // no hay preguntas para sicronizar
        }


    }


    // esta funcion se ejecuta solo cuando haya inconsistencias en las calificaciones
    public function validar_puntajes(){
        $respuestas = DB::SELECT("SELECT DISTINCT sr.id_usuario, sr.id_respuesta_pregunta, sr.id_pregunta, sr.respuesta, sr.puntaje, sp.puntaje_pregunta, so.tipo, if(sr.puntaje>0 AND so.tipo = 0, 'mal' ,'bien') as 'valid' FROM salle_respuestas_preguntas sr, salle_preguntas sp, salle_opciones_preguntas so WHERE sr.id_pregunta = sp.id_pregunta AND sp.id_pregunta = so.id_pregunta AND sr.respuesta = so.id_opcion_pregunta ORDER BY `sr`.`puntaje` ASC");

        foreach ($respuestas as $key => $value) {
            if( $value->valid == "mal" ){
                DB::UPDATE("UPDATE `salle_respuestas_preguntas` SET `puntaje`=0 WHERE `id_respuesta_pregunta` = ?",[$value->id_respuesta_pregunta]);
            }
        }
    }


    public function transformar_preguntas_salle(Request $request){
        $nuevo_tipo = 1;
        if( $request->id_tipo_pregunta == 1 ){ $nuevo_tipo = 5; }

        // return $nuevo_tipo .'___'.$request->id_pregunta;

        DB::UPDATE("UPDATE `salle_preguntas` SET `id_tipo_pregunta`= ? WHERE `id_pregunta` = ?",[$nuevo_tipo, $request->id_pregunta]);

    }


    public function salle_intento_eval(Request $request){
        $periodo = date('Y');
        
        // Se cambia a estado eliminado las evaluaciones que ya haya culminado el estudiante en el periodo actual. 
        // Esto permite que se genere una nueva evaluacion.
        DB::UPDATE("UPDATE `salle_evaluaciones` SET `estado` = 3 WHERE `id_usuario` = $request->idusuario AND `estado` = 2 AND `created_at` LIKE '$periodo%'");
        
    }

    public function modificar_periodo_codigos(){
        
        $codigos = [
            'MMA2-GZQPX51134581',
            'MM3-VVG953039',
            'MM3-GPJ690593',
            'MNA3-XHQB131257',
            'MNA3-DPWQ761894',
            'MNA3-WZMT736313'
        ];
            
            $codigos_no_econtrados = array();
            $cant_modificados = 0;
            for( $i=0; $i<count($codigos); $i++ ){
                DB::UPDATE("UPDATE IGNORE `codigoslibros` SET `id_periodo` = 16, `idusuario` = 29951 WHERE `codigo` = '?'", [$codigos[$i]]);
                
                $codigo = DB::SELECT("SELECT codigo FROM `codigoslibros` WHERE `codigo` = ?", [$codigos[$i]]);
                
                if( !$codigo ){
                    array_push($codigos_no_econtrados,$codigos[$i]);
                }else{
                    $cant_modificados++;
                }
            }

            // return $codigos_no_econtrados;
            return $cant_modificados;
    }


}