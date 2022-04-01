<?php

namespace App\Http\Controllers;

use App\Models\Temporada;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Institucion;
use App\Models\Ciudad;
use App\Models\CodigoLibros;
use App\Models\HistoricoContratos;
use App\Models\Verificacion;
use App\Models\VerificacionHasInstitucion;
use App\Models\CodigosLibros;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;




class TemporadaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //api para el get para milton
    public function temporadaDatos(){
        $temporada = DB::select("select t.* 
        from temporadas t 
       
     "); 

     return $temporada;
    }

    //api institucion para milton

    public function instituciones_facturacion(){
 
    $grupo ="11";
    $estado ="1";
         $institucion_sin_asesor = DB::select("select i.idInstitucion, i.direccionInstitucion, i.cod_contrato, i.telefonoInstitucion, r.nombreregion  as region, c.nombre as ciudad,  u.nombres, u.apellidos
        from institucion i, region r, ciudad c,  usuario u
        where i.region_idregion  = r.idregion
        and i.ciudad_id  =  c.idciudad
        and i.vendedorInstitucion = u.cedula
        and u.id_group <> $grupo
       and i.estado_idEstado  = $estado
     ");
    //para traer las instituciones con asesor
       
    $institucion_con_asesor = DB::select("select i.idInstitucion, i.direccionInstitucion, i.cod_contrato, i.telefonoInstitucion, r.nombreregion  as region, c.nombre as ciudad,  u.nombres, u.apellidos
    from institucion i, region r, ciudad c,  usuario u
    where i.region_idregion  = r.idregion
    and i.ciudad_id  =  c.idciudad
    and i.vendedorInstitucion = u.cedula
    and u.id_group = $grupo
   and i.estado_idEstado  = $estado
 ");

     return ["institucion_con_asesor"=> $institucion_con_asesor,"institucion_sin_asesor"=>$institucion_sin_asesor];
    }
    //api para actualizar la institucion del asesor
    public function asesorInstitucion(Request $request){
   
   
            if($request->idInstitucion){
                //buscar la region
               $institucion=  DB::table('institucion')
                ->select("institucion.region_idregion")
                ->where('idInstitucion',$request->idInstitucion)
                ->get();

                if(count($institucion) <=0){
                    return "No existe la region para la institucion";
                }else{
                    $obtenerRegion = $institucion[0]->region_idregion;

                   
                    if($obtenerRegion == "1"){

                        $res = DB::table('temporadas')
                        ->where('cedula_asesor', $request->cedula_asesor)
                        ->where('contrato',$request->contrato)
                        ->update(['idInstitucion' => $request->idInstitucion, 'temporal_institucion'=>$request->nombre_institucion,'ciudad'=>$request->nombre_ciudad,'temporada'=>'S']);
                        if($res){
                            return "Se guardo correctamente";

                        }else{
                            return "No se pudo guardar";
                        } 
                    }
                    
                    
                    else{
                        $res = DB::table('temporadas')
                        ->where('cedula_asesor', $request->cedula_asesor)
                        ->where('contrato',$request->contrato)
                        ->update(['idInstitucion' => $request->idInstitucion, 'temporal_institucion'=>$request->nombre_institucion,'ciudad'=>$request->nombre_ciudad,'temporada'=>'C']);
                        if($res){
                            return "Se guardo correctamente";

                        }else{
                            return "No se pudo guardar";
                        } 
                    }
                     
                }   

             }
      
     }
    


    //api para un formulario de prueba para  milton
    public function crearliquidacion(Request $request){
    //    $user = Auth::user();
    //     return $user; 
         return view('testearapis.apitemporada');
    }  
    public function eliminarTemporada(Request $request){
     

        $id = $request->get('id_temporada');
        Temporada::findOrFail($id)->delete();
    }
    //api para miton vea los numeros de contratos
    public function show($contrato){

        $contratos =  DB::table('temporadas')
             ->where('contrato', $contrato)
             ->get();
        return $contratos;
    }

    public function index(Request $request)
    {   
        //para editar el asesor una que se que quito el boton edit
        if($request->editarAsesor){

             //para actualizar la institucion el contrato
              DB::table('temporadas')
              ->where('contrato',  $request->contrato)
              ->update([
                  'cedula_asesor' => $request->cedula_asesor,
                  'id_asesor' => $request->id_asesor
                ]);  
            return ["status" => "1", "message" =>"se edito correctamente el asesor"];   
        }
        else{

            $asesores= DB::table('usuario')
            ->select(DB::raw('CONCAT(usuario.nombres , " " , usuario.apellidos ) as asesornombres'),'usuario.idusuario','usuario.nombres','usuario.cedula')
            ->where('id_group', '11')
            ->where('estado_idEstado','1')
            ->get();
        
            $profesores= DB::table('usuario')
                ->select(DB::raw('CONCAT(usuario.nombres , " " , usuario.apellidos ) as  profesornombres'),'usuario.idusuario','usuario.nombres','usuario.cedula')
                ->where('id_group', '6')
                ->where('estado_idEstado','1')
                ->get();
            
        
            $ciudad = Ciudad::all();
            $institucion = Institucion::where('estado_idEstado', '=',1)->get();
            $temporada = DB::select("SELECT t.*, p.descripcion as periodo, CONCAT(ascr.nombres , ' ' , ascr.apellidos ) as asesorProlipa
                from temporadas t 
                LEFT JOIN periodoescolar p ON t.id_periodo = p.idperiodoescolar
                LEFT JOIN usuario ascr  ON ascr.idusuario = t.id_asesor 
            
            "); 
        
            return ['temporada' => $temporada, 'asesores'=> $asesores,'profesores' => $profesores, 'ciudad' => $ciudad, 'listainstitucion' => $institucion];
        }
  

    }
   
    //para traer las instituciones por ciudad
    public function traerInstitucion(Request $request){
        $ciudad = $request->ciudad_id;
        $traerInstitucion = DB::table('institucion')
        ->select('institucion.idInstitucion','institucion.nombreInstitucion','institucion.region_idregion')
        ->where('ciudad_id', $ciudad)
        ->where('estado_idEstado','1')
        ->get();
      return  $traerInstitucion;
   
    
    }
    //para traer los profesores por institucion 
    public function traerprofesores(Request $request){
         $institucion = $request->idInstitucion;
     
        $profesores= DB::table('usuario')
        ->select(DB::raw('CONCAT(usuario.nombres , " " , usuario.apellidos ) as  profesornombres'),'usuario.idusuario','usuario.nombres','usuario.cedula')
        ->where('id_group', '6')
        ->where('institucion_idInstitucion',$institucion)
        ->where('estado_idEstado','1')
        ->get();
        return $profesores;
     
    }
    //para traer los periodos por institucion
    public function traerperiodos(Request $request){
      
        $periodo = $request->region_idregion;
        $estado = $request->condicion;
        $traerPeriodo = DB::table('periodoescolar')
        ->select('periodoescolar.idperiodoescolar',DB::raw('CONCAT(periodoescolar.fecha_inicial , " a " , periodoescolar.fecha_final," | " ,periodoescolar.descripcion ) as  periodo'),'periodoescolar.region_idregion')
        ->OrderBy('periodoescolar.idperiodoescolar','desc') 
        ->where('region_idregion', $periodo)
        ->where('periodoescolar.estado',$estado)
        
        ->get();
         return  $traerPeriodo;
    }


    public function store(Request $request)
    {
          //para buscar  la institucion  y sacar su periodo 
          $verificarperiodoinstitucion = DB::table('periodoescolar_has_institucion')
          ->select('periodoescolar_has_institucion.periodoescolar_idperiodoescolar')

          ->where('periodoescolar_has_institucion.institucion_idInstitucion','=',$request->idInstitucion)
          ->get();
          
           foreach($verificarperiodoinstitucion  as $clave=>$item){
              $verificarperiodos =DB::SELECT("SELECT p.idperiodoescolar
              FROM periodoescolar p
              WHERE p.estado = '1'
              and p.idperiodoescolar = $item->periodoescolar_idperiodoescolar
              ");
           }
       
           if(count($verificarperiodoinstitucion) <=0){
              return ["status"=>"0", "message" => "No existe el periodo lectivo por favor, asigne un periodo a esta institucion"];
          }

    
           //verificar que el periodo exista
          if(count($verificarperiodos) <= 0){
                      
              return ["status"=>"0", "message" => "No existe el periodo lectivo por favor, asigne un periodo a esta institucion"];

           }
         
          else{
                  //almancenar el periodo
               $periodo =  $verificarperiodos[0]->idperiodoescolar;
           
              //para ingresar el historico contratos
              $historico = new HistoricoContratos;
              $historico->contrato = $request->contrato;
              $historico->institucion=  $request->idInstitucion;
              $historico->periodo_id=  $periodo;
              $historico->save();
              

          }
      
         if( $request->id ){
            $temporada = Temporada::find($request->id);
            $temporada->contrato = $request->contrato;
            $temporada->year = $request->year;
            $temporada->ciudad = $request->ciudad;
            $temporada->temporada = $request->temporada;
            $temporada->id_asesor = $request->id_asesor;
            $temporada->cedula_asesor = $request->cedula_asesor;
            $temporada->id_periodo = $periodo;
            
            if($request->id_profesor =="undefined"){
                $temporada->id_profesor = "0";
            }else{
                $temporada->id_profesor = $request->id_profesor;
            }
           
            $temporada->idInstitucion  = $request->idInstitucion;

    
        }else{
        
            $temporada = new Temporada();
            $temporada->contrato = $request->contrato;
            $temporada->year = $request->year;
            $temporada->ciudad = $request->ciudad;
            $temporada->temporada = $request->temporada;
            if($request->id_profesor =="undefined"){
                $temporada->id_profesor = "0";
            }else{
                $temporada->id_profesor = $request->id_profesor;
            }

            if($request->temporal_cedula_docente =="undefined"){
                $temporada->temporal_cedula_docente = "0";
            }else{
                $temporada->temporal_cedula_docente = $request->temporal_cedula_docente;
            }

            if($request->temporal_nombre_docente =="undefined"){
                $temporada->temporal_nombre_docente = "0";
            }else{
                $temporada->temporal_nombre_docente = $request->temporal_nombre_docente;
            }
            
                $temporada->idInstitucion  = $request->idInstitucion;
                $temporada->temporal_institucion  = $request->temporal_institucion;
                $temporada->id_asesor = $request->id_asesor;
                $temporada->cedula_asesor = $request->cedula_asesor;
                $temporada->nombre_asesor = $request->nombre_asesor;
                $historico->periodo_id=  $periodo;
            
        }

        $temporada->save();

        return ["status"=>"0", "message" => "Se agrego correctamente"];
    }
    //api para que los asesores puedan ver sus contratos
    public function asesorcontratos(Request $request){
        $cedula = $request->cedula;

          
        $temporadas= DB::table('temporadas')
            ->select('temporadas.*')
            ->where('cedula_asesor', $cedula)
            ->where('estado','1')
            ->get();

        return $temporadas;
        
    }

    //api:Get>>/liquidacion/contrato
    //api para  hacer la liquidacion
    public function liquidacion($contrato){
  
        set_time_limit(0);

        $buscarInstitucion= DB::table('temporadas')
        ->select('temporadas.idInstitucion')
        ->where('contrato', $contrato)
  
        ->get();
        if(count($buscarInstitucion) <= 0){
            return "no existe la institucion";        

        }else{

            $institucion = $buscarInstitucion[0]->idInstitucion;
          
            //verificar que el periodo exista
            $verificarPeriodo = DB::select("SELECT t.contrato, t.id_periodo, p.idperiodoescolar
             FROM temporadas t, periodoescolar p
             WHERE t.id_periodo = p.idperiodoescolar
             AND contrato = '$contrato'
             ");
             if(empty($verificarPeriodo)){
                return ["status"=>"0", "message" => "No se encontro el periodo"]; 
             }
           
            else{
                //almancenar el periodo
                 $periodo =  $verificarPeriodo[0]->idperiodoescolar;
             
                //traer temporadas
                $temporadas= DB::table('temporadas')
                ->select('temporadas.*')
                ->where('contrato', $contrato)
                ->where('estado','1')
                ->get();       
            
                $data = DB::select("
                CALL`liquidacion_proc`($institucion,$periodo)
            
                ");
                 
                //SI TODO HA SALIDO BIEN TRAEMOS LA DATA 
                if(count($data) >0){
                 return ['temporada'=>$temporadas,'codigos_libros' => $data];
             
                }else{
                    return ["status"=>"0", "message" => "No se pudo cargar la informacion"];
                }
                

            }
        }

    }


     //api para  hacer la liquidacion para MILTON 
     public function liquidacionMilton($contrato){    
        set_time_limit(0);
        $buscarInstitucion= DB::table('temporadas')
        ->select('temporadas.idInstitucion')
        ->where('contrato', $contrato)
  
        ->get();
        if(count($buscarInstitucion) <= 0){
            return "no existe la institucion";     

        }else{
            $institucion = $buscarInstitucion[0]->idInstitucion;

             //verificar que el periodo exista
             $verificarPeriodo = DB::select("SELECT t.contrato, t.id_periodo, p.idperiodoescolar
             FROM temporadas t, periodoescolar p
             WHERE t.id_periodo = p.idperiodoescolar
             AND contrato = '$contrato'
             ");
             if(empty($verificarPeriodo)){
                return ["status"=>"0", "message" => "No se encontro el periodo"]; 
             }
           
            //traer la liquidacion
            else{
                    //almancenar el periodo
                 $periodo =  $verificarPeriodo[0]->idperiodoescolar;
                //traer temporadas
                $temporadas= DB::table('temporadas')
                ->select('temporadas.*')
                ->where('contrato', $contrato)
                ->where('estado','1')
                ->get();
        
            
            $data = DB::select("
            CALL`liquidacion_milton_proc`($institucion,$periodo)
           
            ");        
                if(count($data) >0){
                    return ['temporada'=>$temporadas,'codigos_libros' => $data];
                }else{
                    return ["status"=>"0", "message" => "No se pudo cargar la informacion"];
                }                        

            }
        }
   
    }


    //Api para milton para nos envia la data y nos guarde en nuestra bd
    public function generarApiTemporada(Request $request){

     $contrato= $request->contrato;
     $anio = $request->year;
     $ciudad= $request->ciudad;
     $temporada = $request->temporada;
     $temporal_nombre_docente= $request->temporal_nombre_docente;
     $temporal_cedula_docente= $request->temporal_cedula_docente;
     $temporal_institucion = $request->temporal_institucion;
     $nombre_asesor = $request->nombre_asesor;
     
     
     if(is_null($contrato) || is_null($anio) ||  is_null($ciudad) ||  is_null($temporada) || is_null($temporal_nombre_docente) || is_null($temporal_cedula_docente) ||   is_null($temporal_institucion) ||  is_null($nombre_asesor)   ){
        return "Por favor llene todos los campos";
     }else{

         $verificar_contrato = $request->contrato;
        $verificarcontratos = DB::table('temporadas')
        ->select('temporadas.contrato','temporadas.year')
     
        ->where('temporadas.contrato','=',$verificar_contrato)
        ->get();

        if(count($verificarcontratos) <= 0){
         
        $temporada = new Temporada();
        $temporada->contrato = $request->contrato; 
        $temporada->year = $request->year; 
        $temporada->ciudad = $request->ciudad; 
        $temporada->temporada = $request->temporada; 
        $temporada->temporal_nombre_docente = $request->temporal_nombre_docente; 
        $temporada->temporal_cedula_docente = $request->temporal_cedula_docente; 
        $temporada->temporal_institucion = $request->temporal_institucion; 
        $temporada->nombre_asesor = $request->nombre_asesor;
        //campos a null
        $temporada->id_profesor= "0";
        $temporada->id_asesor= "0";
        $temporada->idInstitucion= "0";
        $temporada->cedula_asesor = "0";
        $date = Carbon::now();   
        $temporada->ultima_fecha = $date;
        $temporada->save();

        return response()->json($temporada);
            
        }else{
            return "ya existe el contrato";
        }
       
     }
      
    }

     public function desactivar(Request $request)
    {    
        $temporada =  Temporada::findOrFail($request->get('id_temporada'));
        $temporada->estado = 2;
        $temporada->save();
        return response()->json($temporada);
    }

     public function activar(Request $request)
    {
        $temporada =  Temporada::findOrFail($request->get('id_temporada'));
        $temporada->estado = 1;
        $temporada->save();
        return response()->json($temporada);
    }
    // funcion para agregar el docente a la vista de temporadas
    public function agregardocente(Request $request){
        $docente = new Usuario();
        $docente->cedula = $request->cedula;
        $docente->nombres = $request->nombres;
        $docente->apellidos = $request->apellidos;
        $docente->email = $request->email;
        $docente->name_usuario = $request->name_usuario;
        $docente->password=sha1(md5($request->cedula));
        $docente->id_group = 6;
        $docente->institucion_idInstitucion  = $request->institucion_idInstitucion;
        $docente->save();

        return $docente;
    }

    //APIS NUEVAS CON BARCODE

    //api de liquidacion para el sistema

     //api de milton liquidacion
     public function bliquidacionSistema(Request $request){
       
            $institucion = $request->institucion_id;
            $periodo     = $request->periodo_id;
            $data = DB::select("SELECT ls.codigo_liquidacion AS codigo,  COUNT(ls.codigo_liquidacion) AS cantidad, c.serie,
            c.libro_idlibro,c.libro as nombrelibro, i.nombreInstitucion , 
            CONCAT(u.nombres, ' ', u.apellidos) AS asesor
               FROM codigoslibros c 
               LEFT JOIN  libros_series ls ON ls.idLibro = c.libro_idlibro
               LEFT JOIN institucion i ON i.idInstitucion = c.bc_institucion
               LEFT JOIN usuario u ON u.cedula = i.vendedorInstitucion
               WHERE c.bc_estado = '2'
               AND c.estado <> 2
               AND c.bc_periodo  = '$periodo'
               AND c.bc_institucion = '$institucion'
               AND ls.idLibro = c.libro_idlibro 
               GROUP BY ls.codigo_liquidacion,c.libro, c.serie,c.libro_idlibro, u.nombres,u.apellidos
            ");        
            return $data;                
    }


    //api de milton liquidacion
    public function bliquidacion_milton($contrato){
       
        set_time_limit(0);
        $buscarInstitucion= DB::SELECT("SELECT  * from temporadas
         WHERE contrato = '$contrato'
         AND estado = '1'
        ");

        if(count($buscarInstitucion) == 0){
            return ["status"=>"0", "message" => "No se encontro el contrato"];    
        }else{
            $institucion = $buscarInstitucion[0]->idInstitucion;
             //verificar que el periodo exista
             $verificarPeriodo = DB::select("SELECT t.contrato, t.id_periodo, p.idperiodoescolar
             FROM temporadas t, periodoescolar p
             WHERE t.id_periodo = p.idperiodoescolar
             AND contrato = '$contrato'
             ");
             if(empty($verificarPeriodo)){
                return ["status"=>"0", "message" => "No se encontro el periodo"]; 
             }
           
            //traer la liquidacion
            else{
                //almancenar el periodo
                $periodo =  $verificarPeriodo[0]->idperiodoescolar;
                //traer temporadas
                $temporadas= $buscarInstitucion;

                $data = DB::select("SELECT ls.codigo_liquidacion AS codigo,  COUNT(ls.codigo_liquidacion) AS cantidad, c.libro as nombrelibro
                FROM codigoslibros c , libros_series ls
                WHERE c.bc_estado = '2'
                AND c.estado <> 2
                AND bc_periodo  = '$periodo'
                AND bc_institucion = '$institucion'
                AND ls.idLibro = c.libro_idlibro 
                GROUP BY ls.codigo_liquidacion,c.libro 
           
            ");        
                if(count($data) >0){
                    return ['temporada'=>$temporadas,'codigos_libros' => $data];
                }else{
                    return ["status"=>"0", "message" => "No se pudo cargar la informacion"];
                }                        

            }
        }
    }
}
