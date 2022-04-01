<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Ciudad;
use App\Models\PeriodoInstitucion;
use Illuminate\Http\Request;
use DB;
use App\Quotation;
use App\Models\Configuracion_salle;

class InstitucionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function index()
    {
        $institucion = DB::select("CALL `listar_instituciones_periodo_activo` ();");
        return $institucion;
        
    }
    public function traerInstitucion(Request $request){
        $institucion = DB::select("SELECT * FROM institucion WHERE  idInstitucion = $request->institucion_idInstitucion
        ");
        return $institucion;
    }

    public function selectInstitucion(Request $request){
        if(empty($request->idregion) && empty($request->idciudad)){
            $institucion = DB::SELECT("SELECT idInstitucion,UPPER(nombreInstitucion) as nombreInstitucion FROM institucion WHERE idInstitucion != 66 AND estado_idEstado = 1");
        }
        if(!empty($request->idregion) && empty($request->idciudad)){
            $institucion = DB::SELECT("SELECT idInstitucion,UPPER(nombreInstitucion) as nombreInstitucion FROM institucion WHERE region_idregion = ? AND idInstitucion != 66 AND estado_idEstado = 1",[$request->idregion]);
        }
        if(!empty($request->idciudad) && empty($request->idregion)){
            $institucion = DB::SELECT("SELECT idInstitucion,UPPER(nombreInstitucion) as nombreInstitucion FROM institucion WHERE ciudad_id = ? AND idInstitucion != 66 AND estado_idEstado = 1",[$request->idciudad]);
        }
        if(!empty($request->idciudad) && !empty($request->idregion)){
            $institucion = DB::SELECT("SELECT idInstitucion,UPPER(nombreInstitucion) as nombreInstitucion FROM institucion WHERE ciudad_id = ? AND region_idregion = ? AND idInstitucion != 66 AND estado_idEstado = 1",[$request->idciudad,$request->idregion]);
        }
        return $institucion;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datosValidados=$request->validate([
            'nombreInstitucion' => 'required',
            'telefonoInstitucion' => 'required',
            'direccionInstitucion' => 'required',
            'vendedorInstitucion' => 'required',
            'region_idregion' => 'required',
            'solicitudInstitucion' => 'required',
            'ciudad_id' => 'required',
            'tipo_institucion' => 'required',
        ]);

        $ruta = public_path('/instituciones_logos');
        if(!empty($request->file('imagenInstitucion'))){
            $file = $request->file('imagenInstitucion');
            $fileName = uniqid().$file->getClientOriginalName();
            $file->move($ruta,$fileName);
            $cambio->imagenInstitucion = $fileName;
        }

        if(!empty($request->idInstitucion)){
            // $institucion = Institucion::find($request->idInstitucion)->update($request->all());
            $cambio = Institucion::find($request->idInstitucion);
            // return $institucion;
        }
        else{
            $cambio = new Institucion();
        }
        //     $institucion = new Institucion($request->all());
        //     $institucion->save();
            // $periodoInstitucion = new PeriodoInstitucion();
            // $periodoInstitucion->institucion_idInstitucion = $institucion->idInstitucion;
            // $periodoInstitucion->estado_idEstado  = '1';
            // $periodoInstitucion->save();
        $cambio->idcreadorinstitucion = $request->idcreadorinstitucion;
        $cambio->nombreInstitucion = $request->nombreInstitucion;
        $cambio->direccionInstitucion = $request->direccionInstitucion;
        $cambio->telefonoInstitucion = $request->telefonoInstitucion;
        $cambio->solicitudInstitucion = $request->solicitudInstitucion;
        $cambio->vendedorInstitucion = $request->vendedorInstitucion;
        $cambio->tipo_institucion = $request->tipo_institucion;
        $cambio->region_idregion = $request->region_idregion;
        $cambio->ciudad_id = $request->ciudad_id;
        $cambio->save();
        return $cambio;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Institucion  $institucion
     * @return \Illuminate\Http\Response
     */
    public function show(Institucion $institucion)
    {
        return $institucion;
    }


    public function verInstitucionCiudad($idciudad)
    {   
        $instituciones = DB::SELECT("SELECT idInstitucion as id, nombreInstitucion as label FROM institucion WHERE idInstitucion != 66 AND ciudad_id = $idciudad");

        return $instituciones;
    }

    
    public function verificarInstitucion($id)
    {   
        $instituciones = DB::SELECT("SELECT u.institucion_idInstitucion, i.aplica_matricula FROM usuario u, institucion i WHERE u.idusuario = $id AND u.institucion_idInstitucion = i.idInstitucion");

        return $instituciones;
    }

    
    public function asignarInstitucion(Request $request)
    {   
        $institucion = DB::UPDATE("UPDATE usuario SET institucion_idInstitucion = $request->institucion WHERE idusuario = $request->usuario");

        return $institucion;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Institucion  $institucion
     * @return \Illuminate\Http\Response
     */
    public function edit(Institucion $institucion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Institucion  $institucion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Institucion $institucion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Institucion  $institucion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Institucion $institucion)
    {
        $institucion = Institucion::find($institucion->idarea)->update(['estado_idEstado' => '0']);
        return $institucion;
    }


    //guardar foto de institucion desde perfil de director
    public function guardarLogoInstitucion(Request $request)
    {
        $cambio = Institucion::find($request->institucion_idInstitucion);

        $ruta = public_path('/instituciones_logos');
        if(!empty($request->file('archivo'))){
        $file = $request->file('archivo');
        $fileName = uniqid().$file->getClientOriginalName();
        $file->move($ruta,$fileName);
        $cambio->imgenInstitucion = $fileName;
        }

        $cambio->ideditor = $request->ideditor;
        $cambio->nombreInstitucion = $request->nombreInstitucion;
        $cambio->direccionInstitucion = $request->direccionInstitucion;
        $cambio->telefonoInstitucion = $request->telefonoInstitucion;
        $cambio->region_idregion = $request->region_idregion;
        $cambio->ciudad_id = $request->ciudad_id;
        $cambio->updated_at = now();
        
        $cambio->save();
        return $cambio;

    }
    public function institucionesSalle()
    {
        // $institucion = DB::select("SELECT nombreInstitucion, idInstitucion FROM  institucion  WHERE tipo_institucion = 2 and estado_idEstado = 1 ");
        // return $institucion;

        $institucion = DB::select("SELECT i.nombreInstitucion, i.idInstitucion, concat(i.nombreInstitucion,' - ',c.nombre) AS institucion_ciudad 
        FROM  institucion i  
        INNER JOIN ciudad c ON i.ciudad_id = c.idciudad
        WHERE i.tipo_institucion = 2 and i.estado_idEstado = 1 ");
        return $institucion;
    }

    public function instituciones_salle(){
        $instituciones = DB::SELECT("SELECT i.*, c.nombre as nombre_ciudad, concat(i.nombreInstitucion,' - ',c.nombre) AS institucion_ciudad, sc.fecha_inicio, sc.fecha_fin, sc.ver_respuestas, sc.observaciones, sc.cant_evaluaciones FROM institucion i INNER JOIN ciudad c ON i.ciudad_id = c.idciudad LEFT JOIN salle_configuracion sc ON i.id_configuracion = sc.id_configuracion WHERE i.tipo_institucion = 2");
        
        if(!empty($instituciones)){
            foreach ($instituciones as $key => $value) {
                $periodo = DB::SELECT("SELECT p.idperiodoescolar, p.fecha_inicial, p.fecha_final, p.periodoescolar, p.estado FROM periodoescolar_has_institucion pi, periodoescolar p WHERE pi.institucion_idInstitucion = ? AND pi.periodoescolar_idperiodoescolar = p.idperiodoescolar ORDER BY p.idperiodoescolar DESC LIMIT 1",[$value->idInstitucion]);
                
                $data['items'][$key] = [
                    'institucion' => $value,
                    'periodo' => $periodo,
                ];            
            }
        }else{
            $data = [];
        }
        return $data;
    }

    public function instituciones_salle_select(){
        $instituciones = DB::SELECT("SELECT i.*, c.nombre as nombre_ciudad, concat(i.nombreInstitucion,' - ',c.nombre) AS institucion_ciudad, sc.fecha_inicio, sc.fecha_fin, sc.ver_respuestas, sc.observaciones, sc.cant_evaluaciones FROM institucion i INNER JOIN ciudad c ON i.ciudad_id = c.idciudad LEFT JOIN salle_configuracion sc ON i.id_configuracion = sc.id_configuracion WHERE i.tipo_institucion = 2");

        return $instituciones;
    }

    public function save_instituciones_salle(Request $request){
        if( $request->id_configuracion == 0 ){
            $configuracion = new Configuracion_salle();
        }else{
            $configuracion = Configuracion_salle::find($request->id_configuracion);
        }
        
        $configuracion->fecha_inicio = $request->fecha_inicio;
        $configuracion->fecha_fin = $request->fecha_fin;
        $configuracion->ver_respuestas = $request->ver_respuestas;
        $configuracion->observaciones = $request->observaciones;
        $configuracion->cant_evaluaciones = $request->cant_evaluaciones;

        $configuracion->save();

        if( $request->id_configuracion == 0 ){
            DB::UPDATE("UPDATE `institucion` SET `id_configuracion` = $configuracion->id_configuracion WHERE `idInstitucion` = $request->id_institucion");
        }
        
        return $configuracion;
    }
    public function listaInstitucionesActiva(){

        $institucion = DB::SELECT("SELECT inst.idInstitucion,
        UPPER(inst.nombreInstitucion) as nombreInstitucion,
        UPPER(ciu.nombre) as ciudad,
        UPPER(reg.nombreregion) as nombreregion,
        inst.solicitudInstitucion,
        -- inst.vendedorInstitucion as asesor
        concat_ws(' ', usu.nombres, usu.apellidos) as asesor

        
        FROM institucion inst, ciudad ciu, region reg, usuario usu
        where inst.ciudad_id = ciu.idciudad
        AND inst.region_idregion = reg.idregion
        AND inst.vendedorInstitucion = usu.cedula
        AND inst.estado_idEstado = 1");
                return $institucion;
    }
    public function institucionConfiguracionSalle($id)
    {
        $configuracion = DB::SELECT("SELECT inst.id_configuracion, sc.* 
        FROM institucion inst, salle_configuracion sc
        WHERE inst.id_configuracion = sc.id_configuracion
        AND inst.idInstitucion  = $id");
        return $configuracion;
    }
    public function listaInsitucion(Request $request)
    {
        // $lista = Institucion::select(['idInstitucion','nombreInstitucion','solicitudInstitucion','estado_idEstado']);
        $lista = Institucion::select('institucion.idInstitucion','institucion.nombreInstitucion','institucion.aplica_matricula','institucion.solicitudInstitucion','estado.nombreestado as estado','ciudad.nombre as ciudad','usuario.idusuario as asesor_id','usuario.nombres as nombre_asesor', 'usuario.apellidos as apellido_asesor', 'institucion.fecha_registro', 'region.nombreregion' )
        ->leftjoin('ciudad','institucion.ciudad_id','=','ciudad.idciudad')
        ->leftjoin('region','institucion.region_idregion','=','region.idregion')
        ->leftjoin('usuario','institucion.vendedorInstitucion','=','usuario.cedula')
        ->join('estado','institucion.estado_idEstado','=','estado.idEstado')
        ->where('institucion.nombreInstitucion','like','%'.$request->busqueda.'%')
        ->orderBy('institucion.fecha_registro','desc')
        ->get();
        if(count($lista) ==0){
            return ["status" => "0","message"=> "No se encontro instituciones con ese nombre"];
        }else{
            return $lista;
        }
        
    }

    public function listaInsitucionAsesor(Request $request)
    {
        // $lista = Institucion::select(['idInstitucion','nombreInstitucion','solicitudInstitucion','estado_idEstado']);
        $lista = Institucion::select('institucion.idInstitucion','institucion.nombreInstitucion','institucion.aplica_matricula','institucion.solicitudInstitucion','estado.nombreestado as estado','ciudad.nombre as ciudad','usuario.idusuario as asesor_id','usuario.nombres as nombre_asesor', 'usuario.apellidos as apellido_asesor', 'institucion.fecha_registro', 'region.nombreregion' )
        ->leftjoin('ciudad','institucion.ciudad_id','=','ciudad.idciudad')
        ->leftjoin('region','institucion.region_idregion','=','region.idregion')
        ->leftjoin('usuario','institucion.vendedorInstitucion','=','usuario.cedula')
        ->join('estado','institucion.estado_idEstado','=','estado.idEstado')
        ->where('institucion.nombreInstitucion','like','%'.$request->busqueda.'%')
        ->where('institucion.vendedorInstitucion','=',$request->cedula)
        ->orderBy('institucion.fecha_registro','desc')
        ->get();
        if(count($lista) ==0){
            return ["status" => "0","message"=> "Esta institución no esta asignada a su perfil"];
        }else{
            return $lista;
        }
        
    }
}