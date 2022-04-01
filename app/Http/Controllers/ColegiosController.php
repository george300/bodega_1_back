<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Libro;
use App\Models\Area;
use App\Models\RecursosColegio;
use App\Models\Planificacion;
use App\Models\Juegos;
use App\Models\ColegioHasRecurso;
use App\Models\AsignaturaColegio;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class ColegiosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //select areas 
    public function selectArea(Request $request)
    {
        $area = Area::all();
        foreach ($area as $key => $post) {
            $respuesta = DB::SELECT("SELECT idasignatura as id, nombreasignatura as name ,tipo_asignatura 
            FROM asignatura
            join area on area.idarea = asignatura.area_idarea 
            WHERE area_idarea = ? 
            AND tipo_asignatura = '1'
            ",[$post->idarea]
            );
            $data['items'][$key] = [
                'id' => "a".$post->idarea,
                'name' => $post->nombrearea,
                'children'=>$respuesta,
            ];
        }
        return $data;
    }
    //para ingresar asignaturas al colegio
    public function asignar_asignatura_colegio(Request $request)
    {
        $dato = DB::table('colegio_asignatura')
        ->where('institucion_id','=',$request->institucion_id)
        ->where('asignatura_id','=',$request->asignatura_id)
        ->where('estado','=','1')
        ->get();
        if ($dato->count() > 0) {
            return $dato->count();
        }else{

            if($request->colegio_asignatura_id){
                $asignatura = AsignaturaColegio::findOrFail($request->colegio_asignatura_id);
                $asignatura->libroweb = $request->libroweb;
                $asignatura->libro_con_guia = $request->libro_con_guia; 
                $asignatura->guia_didactica = $request->guia_didactica;
                $asignatura->unidades = $request->unidades;  
                $asignatura->agregar = $request->agregar;  
                $asignatura->editar = $request->editar;  
                $asignatura->eliminar = $request->eliminar; 
                $asignatura->ver = $request->ver;  
                $asignatura->cuaderno_con_guia = $request->cuaderno_con_guia;  
                $asignatura->cuaderno_guia_didactica = $request->cuaderno_guia_didactica; 
                $asignatura->cuaderno_web = $request->cuaderno_web;    
                $asignatura->planificacion_descargar = $request->planificacion_descargar; 
                $asignatura->planificacion_visualizar = $request->planificacion_visualizar; 
                $asignatura->r_adicional_zona_diversion = $request->r_adicional_zona_diversion;
                $asignatura->r_adicional_material = $request->r_adicional_material;
                $asignatura->r_adicional_propuestas = $request->r_adicional_propuestas;
                $asignatura->r_adicional_adaptaciones = $request->r_adicional_adaptaciones;
                $asignatura->r_adicional_articulos = $request->r_adicional_articulos;
                $asignatura->r_adicional_glosario = $request->r_adicional_glosario;
                $asignatura->r_adicional_simulador = $request->r_adicional_simulador;
                $asignatura->save();

                //para editar los otros recursos
                if($asignatura->save()){
                    
                    DB::table('colegios_has_recursos')
                    ->where('colegio_asignatura_id', $request->colegio_asignatura_id)
                    ->update([
                        "permisos" => $request->juegosArreglo,
                      
                    ]);
                }
            }else{
                $asignatura = new AsignaturaColegio();
                $asignatura->institucion_id = $request->institucion_id;
                $asignatura->asignatura_id = $request->asignatura_id;    
                $asignatura->libroweb = $request->libroweb;
                $asignatura->libro_con_guia = $request->libro_con_guia; 
                $asignatura->guia_didactica = $request->guia_didactica;
                $asignatura->unidades = $request->unidades; 
                $asignatura->agregar = $request->agregar;  
                $asignatura->editar = $request->editar;  
                $asignatura->eliminar = $request->eliminar; 
                $asignatura->ver = $request->ver;  
                $asignatura->cuaderno_con_guia = $request->cuaderno_con_guia;  
                $asignatura->cuaderno_guia_didactica = $request->cuaderno_guia_didactica; 
                $asignatura->cuaderno_web = $request->cuaderno_web;
                $asignatura->planificacion_descargar = $request->planificacion_descargar; 
                $asignatura->planificacion_visualizar = $request->planificacion_visualizar; 
                $asignatura->r_adicional_zona_diversion = $request->r_adicional_zona_diversion;
                $asignatura->r_adicional_material = $request->r_adicional_material;
                $asignatura->r_adicional_propuestas = $request->r_adicional_propuestas;
                $asignatura->r_adicional_adaptaciones = $request->r_adicional_adaptaciones;
                $asignatura->r_adicional_articulos = $request->r_adicional_articulos;
                $asignatura->r_adicional_glosario = $request->r_adicional_glosario;
                $asignatura->r_adicional_simulador = $request->r_adicional_simulador;               
                $asignatura->save();

                //Para guardar los recursos de zona de diversion
                if($asignatura->save()){
                    $zona_diversion = new ColegioHasRecurso();
                    $zona_diversion->colegio_asignatura_id = $asignatura->colegio_asignatura_id;
                    $zona_diversion->asignatura_id = $asignatura->asignatura_id;
                    $zona_diversion->institucion_id = $request->institucion_id;
                    $zona_diversion->recurso_id = 1;
                    $zona_diversion->permisos = $request->juegosArreglo;
                    $zona_diversion->save();
                }     
            }
           return $asignatura;
        }
    }

    //para traer el listado del las asignaturas del colegio
    public function asignaturas_x_colegio(Request $request)
    {
        $dato = DB::table('colegio_asignatura as ausu')
        ->where('ausu.institucion_id','=',$request->institucion_id)
        ->leftjoin('asignatura as asig','ausu.asignatura_id','=','asig.idasignatura')
        ->leftjoin('colegios_has_recursos as clr','ausu.colegio_asignatura_id','=','clr.colegio_asignatura_id')
        ->select('asig.nombreasignatura','asig.idasignatura','asig.area_idarea', 'ausu.institucion_id','ausu.asignatura_id','ausu.colegio_asignatura_id as idasignado',
        'ausu.libroweb','ausu.libro_con_guia' , 'ausu.guia_didactica' , 'ausu.unidades', 'ausu.estado','ausu.agregar','ausu.editar','ausu.eliminar','ausu.ver','ausu.cuaderno_con_guia','ausu.cuaderno_guia_didactica','ausu.cuaderno_web','ausu.planificacion_descargar','ausu.planificacion_visualizar','ausu.r_adicional_zona_diversion','ausu.r_adicional_material','ausu.r_adicional_propuestas','ausu.r_adicional_adaptaciones','ausu.r_adicional_articulos','ausu.r_adicional_glosario','ausu.r_adicional_simulador','clr.permisos')
        ->get();
        return $dato;
    }

    //para traer los permisos 
    //api:get//>/colegios/permisos
    public function permisos(Request $request){
        $permisos = DB::SELECT("SELECT * FROM colegios_has_recursos c
        WHERE c.institucion_id = '$request->institucion_id'
        AND  c.asignatura_id = '$request->asignatura_id'
        AND c.estado = '1'");
        return $permisos;
    }

    public function eliminaAsignacionColegio($id)
    {
        $data = AsignaturaColegio::find($id);
        $data->delete();
        return $data;
    }

    public function index(Request $request)
    {
        // return csrf_token();
        set_time_limit(60000);
        ini_set('max_execution_time', 60000);
  
        // $libros = DB::select("SELECT libro.*, asignatura.nombreasignatura as asignatura
        // FROM libro, asignatura
        // WHERE Estado_idEstado = '1'
        // and libro.asignatura_idasignatura  = asignatura.idasignatura 
        // ");
    
        // foreach($libros as $key => $item){
        //   $recurso = new RecursosColegio;
        //   $recurso->recurso_id = $item->idlibro;
        //   $recurso->nombre_recurso = $item->nombrelibro;
        //   $recurso->idasignatura = $item->asignatura_idasignatura;
        
        //   $recurso->tipo_recurso = 1;
        //   $recurso->save();
        // }

  
        // $planificacion = DB::select("SELECT planificacion.*, asignatura.nombreasignatura as asignatura
        // FROM planificacion, asignatura
        // WHERE Estado_idEstado = '1'
        // and planificacion.asignatura_idasignatura  = asignatura.idasignatura 
        // ");


   
        //     foreach($planificacion as $key => $item){
        //         // $recurso = new RecursosColegio;
        //         // $recurso->recurso_id = $item->idplanificacion;
        //         // $recurso->nombre_recurso = $item->descripcionplanificacion;
        //         // $recurso->idasignatura = $item->asignatura_idasignatura;
              
        //         // $recurso->tipo_recurso = 2;
        //         // $recurso->save();


        //         $recurso = RecursosColegio::create([
        //             'recurso_id' => $item->idplanificacion,
        //             'nombre_recurso' => $item->descripcionplanificacion,
        //             'tipo_recurso' => "2",
        //             'idasignatura' => $item->asignatura_idasignatura
        //         ]);

        //         }

          
                
           

        // $juegos = Juegos::all();
   

        //     foreach($juegos as $key => $item){
        //         $recurso = new RecursosColegio;
        //         $recurso->recurso_id = $item->idjuegos;
        //         $recurso->nombre_recurso = $item->nombre;
        //         $recurso->idasignatura = $item->asignatura_idasignatura;
               
        //         $recurso->tipo_recurso = 4;
        //         $recurso->save();
        //         }

    //     $client = new Client([
    //         'base_uri'=> 'https://foro.prolipadigital.com.ec',
    //         // 'timeout' => 60.0,

    //     ]); 

    //     $response = $client->request('GET', '/adaptaciones-curriculares?estado=true');
    //    $adaptaciones =   json_decode($response->getBody()->getContents());
      
      
    //         foreach($adaptaciones as $key => $item){

               
                // $recurso = new RecursosColegio;
                
                // echo $item->id ."<br>";
                // echo $item->asignatura->idasignatura ."<br>";
               
                // $recurso->recurso_id = $item->id;
                // $recurso->nombre_recurso = $item->nombre;
                // $recurso->tipo_recurso = 3;
                // $recurso->idasignatura = $item->asignatura->idasignatura;
                
                // $recurso->save();


                // $recurso = RecursosColegio::create([
                //     'recurso_id' => $item->id,
                //     'nombre_recurso' => $item->nombre,
                //     'tipo_recurso' => "3",
                //     'idasignatura' => $item->asignatura->idasignatura
                // ]);
                
                // }

            
//     $client = new Client([
//         'base_uri'=> 'https://foro.prolipadigital.com.ec',
       

//     ]); 

//     $response = $client->request('GET', '/proyectos-asignaturas?estado=true');
//    $proyectos =   json_decode($response->getBody()->getContents());

//                 foreach($proyectos as $key => $item){
//                 $recurso = new RecursosColegio;
            
//                 $recurso->recurso_id = $item->id;
//                 $recurso->nombre_recurso = $item->idproyecto;
//                 $recurso->idasignatura = $item->idasignatura;
//                 $recurso->tipo_recurso = 5;
//                 $recurso->save();
//                 }
        
        
        // if($recurso){
        //     return "se guardo correctamente";
        // }else{
        //     return "No se pudo guardar";
        //  }

      
        

    }

 

    //metodo para listar los libros una vez que ingresen api:/colegio-libros
    public function listadoLibros(Request $request){
        //para los accesos al libros
        if($request->accesoLibros){
            $recursos = DB::select("SELECT * FROM colegio_asignatura 
            WHERE institucion_id = $request->institucion
            AND asignatura_id = $request->idasignatura
            AND estado = '1'
            ");
        return $recursos;     
        }
        //para los accesos a las Planificaciones
       
        else{ 
            $institucion = $request->institucion_idInstitucion;
            $buscarInstitucion = DB::select("SELECT DISTINCT c.institucion_id, c.asignatura_id
               FROM  colegio_asignatura   c
               WHERE c.institucion_id = $institucion
               and estado = '1'
            ");
          if(!empty($buscarInstitucion)){
                foreach($buscarInstitucion as $key => $item){
                    $recursos["items"][$key] = DB::select("SELECT DISTINCT libro.idlibro, libro.nombrelibro, libro.descripcionlibro, libro.portada, libro.weblibro, libro.exelibro,libro.pdfsinguia, libro.pdfconguia, libro.guiadidactica, asignatura.idasignatura, asignatura.nombreasignatura
                    FROM colegio_asignatura c,libro ,asignatura
                    WHERE c.asignatura_id = libro.asignatura_idasignatura 
                    and libro.asignatura_idasignatura = $item->asignatura_id
                    and  c.asignatura_id = asignatura.idasignatura 
                    and libro.Estado_idEstado  = '1'
                    and c.institucion_id = $item->institucion_id
                    ");
                }
                return $recursos;  
          }else{
            return ["status" => "0", "message" => "No se encontraron recursos"];
          }
       
            
        }
  
    }

   

    public function ingreso(Request $request){
        // return csrf_token();
        $input = $request->all();
        
        $this->validate($request,[
          
           "email" => 'required|string',
       
        ]);

        $correo = filter_var($request->email, FILTER_VALIDATE_EMAIL);
        
        if(!$correo){
            return response()->json([
                'status' => 'invalid_credentials',
                'message' => 'Correo  no válido.',
            ], 401);
        }

         $user = DB::table('usuario')
             ->where('email', '=', $correo)
             ->take(1)
             ->get();

          $user  = User::where('email', $correo)
            
             ->take(1)
             ->get();
        
     
        if(count($user)<=0){
            return response()->json([
                 'status' => 'invalid_credentials',
                 'message' => 'Correo  no válido.',
             ], 401);
        }

    //   return $user;
   
        foreach($user as $key => $item){
            $token  = csrf_token();

        $data =[
            "idusuario" => $item->idusuario,
            "cedula"=>  $item->cedula,
            "nombres"=>  $item->nombres,
            "apellidos"=>  $item->apellidos,
            "name_usuario"=>  $item->name_usuario,
            "email"=>  $item->email,
            "id_group" => "14",
            "p_ingreso"=> 0,
            "institucion_idInstitucion" =>$item->institucion_idInstitucion,
            "estado_idEstado" => 1,
            "idcreadorusuario" => 5103,
            
            "password_status" =>$item->password_status,
            "session_id" => $item->session_id,
            "foto_user" => $item->foto_user,
            "telefono" => $item->telefono,
            "updated_at" => $item->updated_at,
            "created_at" => $item->created_at,
            "token" => $token

            
        ];
        }
        return $data;


        
    
    }

  
    
}
