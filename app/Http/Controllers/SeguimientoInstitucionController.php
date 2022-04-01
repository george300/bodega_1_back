<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\Request;
use DB;
use App\Models\SeguimientoMuestraDetalle;
use App\Models\SeguimientoInstitucion;
use App\Models\SeguimientoInstitucionTemporal;
use App\Models\SeguimientoMuestra;

class SeguimientoInstitucionController extends Controller
{
      
    public function makeid(){
        $characters = '123456789abcdefghjkmnpqrstuvwxyz';
        $charactersLength = strlen($characters);
   
        $randomString = '';
        for ($i = 0; $i < 5; $i++) {
            for ($i = 0; $i < 16; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
      
        
         }   
    }
    public function muestra(Request $request){
        //$muestras = json_decode($request->muestras);   

        if($request->muestra_id){
       
            $seguimiento = SeguimientoMuestra::findOrFail($request->muestra_id);
        
            $seguimiento->fecha_entrega       = $request->fecha_entrega;
            $seguimiento->observacion         = $request->observacion;
            $seguimiento->persona_solicita    =$request->persona_solicita;
            if($request->finalizar){
                $seguimiento->estado            = "1";
            }
         
            if($request->admin){
                $seguimiento->usuario_editor  = $request->usuario_editor;
            }
            $seguimiento->save();
    
           }else{
               $encontrarNumeroMuestra = $this->listadoSeguimientoMuestra($request->institucion_id,$request->asesor_id,$request->periodo_id);
               if($encontrarNumeroMuestra["status"] == 0){
                 $contador = 1;
               }else{
                  $contador = $encontrarNumeroMuestra["datos"][0]->num_muestra+1;
               }
               $seguimiento = new SeguimientoMuestra();
               $seguimiento->num_muestra   = $contador;
               $seguimiento->fecha_entrega   =     $request->fecha_entrega;
               $seguimiento->observacion           = $request->observacion;
               $seguimiento->institucion_id        = $request->institucion_id;
               $seguimiento->asesor_id             = $request->asesor_id;
               $seguimiento->periodo_id            = $request->periodo_id;
               $seguimiento->persona_solicita      = $request->persona_solicita;
               if($request->admin){
                   $seguimiento->usuario_editor  = $request->usuario_editor;
               }
               $seguimiento->save();

               $traercodigo = $this->makeid();
               $files = $request->file('archivo');
               foreach($files as $clave => $file){
             
                   $path = "/archivos/seguimiento/muestra";
                   $filename = $traercodigo."".$file->getClientOriginalName();
                    if($file->move(public_path().$path,$filename)){
           
                       SeguimientoMuestraDetalle::create([
                           
                               "muestra_id" => $seguimiento->muestra_id,
                               "libro_id" => $request->libro[$clave],
                               'cantidad' => $request->cantidad[$clave],
                               "evidencia" => $traercodigo."".$file->getClientOriginalName()
                           ]);
                   }
               }
           }
           $seguimiento->save();
           if($seguimiento){
               return ["status" => "1", "message" => "Se guardo correctamente"];
           }else{
            return ["status" => "0", "message" => "No se pudo guardar"];
           }
  
    }

    //para marcar como registrado
      public function registrar(Request $request){
        $seguimiento = Agenda::findOrFail($request->id);
        $seguimiento->estado = "1";
        $seguimiento->save();
        
     }
    public function index(Request $request)
    {
        if($request->pendientes){
            $institucionesProlipa = $this->pendientesProlipa($request->asesor_id);
            $institucionesTemporales = $this->pendientesProlipaTemporales($request->asesor_id);
            return[
                "institucionesProlipa" => $institucionesProlipa,
                "institucionesTemporales" => $institucionesTemporales
            ];
        }
        if($request->registrarPendiente){
            $agenda =  Agenda::findOrFail($request->id);
            $agenda->estado = "1";
            $agenda->save();
            if($agenda){
                return ["status" => "1", "message" => "Se guardo correctamente"];
            }else{
             return ["status" => "0", "message" => "No se pudo guardar"];
            }
        }
        else{
            $asesores = DB::SELECT("SELECT DISTINCT u.idusuario,u.cedula, CONCAT(u.nombres, ' ', u.apellidos) as vendedor,
            (SELECT  COUNT(a.id_usuario)  FROM agenda_usuario a
            WHERE a.id_usuario = u.idusuario
            AND u.id_group = '11' 
            AND a.estado = '0'
            ) as  registros_pendientes
            FROM usuario u, institucion i
            WHERE u.id_group = '11'
            AND i.vendedorInstitucion = u.cedula
            AND u.estado_idEstado  ='1'
            ORDER BY u.apellidos ASC
            ");
            return $asesores;
        }
    }

    //api::get>>/asesor/seguimiento
    public function visitas(Request $request){
        if($request->muestra){    
            $muestras = $this->listadoMuestras($request->institucion_id,$request->asesor_id,$request->periodo_id);
            return $muestras;
        }
       
        else{
            $seguimiento = $this->listadoSeguimiento($request->institucion_id,$request->asesor_id,$request->periodo_id);
            return $seguimiento;
        }
   
    }
    //para eliminar la visita / capacitacino/presentacion
     public function eliminar(Request $request){
        $seguimiento = SeguimientoInstitucion::findOrFail($request->id);
        $seguimiento->estado = "2";
        $seguimiento->save();
        
     }
    //para guardar la institucion
    public function GuardarInstitucionTemporal(Request $request){
  
        //obtener el periodo de la region
        $buscarPeriodo = $this->periodosActivosIndividual($request->region);
        $periodo = $buscarPeriodo[0]->idperiodoescolar;
        $institucion = new SeguimientoInstitucionTemporal;
        $institucion->nombre_institucion = $request->nombre_institucion;
        $institucion->ciudad = $request->ciudad;
        $institucion->region = $request->region;
        $institucion->asesor_id = $request->asesor_id;
        $institucion->periodo_id = $periodo;
        $institucion->save();
        return $institucion;
       
    }

    public function periodosActivosIndividual($region){
        $periodo = DB::SELECT("SELECT DISTINCT  p.* FROM periodoescolar p
        LEFT JOIN  codigoslibros c ON p.idperiodoescolar  = c.id_periodo
        WHERE  p.estado = '1'
        AND p.region_idregion = '$region'
        ");
        return $periodo;
    }

    public function guardarSeguimiento(Request $request){
        if($request->id){
       
            $seguimiento = SeguimientoInstitucion::findOrFail($request->id);
        
            $seguimiento->fecha_genera_visita   = $request->fecha_genera_visita;
            $seguimiento->observacion           = $request->observacion;
            if($request->finalizar){
                $seguimiento->estado            = "1";
                $seguimiento->fecha_que_visita  = $request->fecha_que_visita;
            }
         
            if($request->admin){
                $seguimiento->usuario_editor  = $request->usuario_editor;
            }
            $seguimiento->save();
    
           }else{
               $encontrarNumeroVisita = $this->listadoSeguimientoTipo($request->institucion_id,$request->asesor_id,$request->periodo_id,$request->tipo_seguimiento);
               if($encontrarNumeroVisita["status"] == 0){
                 $contador = 1;
               }else{
                  $contador = $encontrarNumeroVisita["datos"][0]->num_visita+1;
               }
               $seguimiento = new SeguimientoInstitucion;
               $seguimiento->num_visita   = $contador;
               $seguimiento->fecha_genera_visita   = $request->fecha_genera_visita;
               if($request->observacion == "null"){
                $seguimiento->observacion = "";    
                }else{
                    $seguimiento->observacion = $request->observacion;   
                }
                if($request->observacion == "null" ||  $request->observacion == "" ){
                  $seguimiento->institucion_id = "";    
                }else{
                     $seguimiento->institucion_id = $request->institucion_id;   
                }
               $seguimiento->observacion           = $request->observacion;
               $seguimiento->asesor_id             = $request->asesor_id;
               $seguimiento->tipo_seguimiento      = $request->tipo_seguimiento;;
               $seguimiento->periodo_id            = $request->periodo_id;
               if($request->admin){
                   $seguimiento->usuario_editor  = $request->usuario_editor;
               }
               $seguimiento->save();
           }
           $seguimiento->save();
           if($seguimiento){
               return ["status" => "1", "message" => "Se guardo correctamente"];
           }else{
            return ["status" => "0", "message" => "No se pudo guardar"];
           }
    }


    public function listadoSeguimiento($institucion_id,$asesor_id,$periodo_id){
        $visitas = DB::SELECT("SELECT  s.* FROM seguimiento_cliente s
        WHERE s.institucion_id = '$institucion_id'
        AND s.asesor_id = '$asesor_id'
        AND s.periodo_id = '$periodo_id'
        AND s.estado <> 2
        ORDER BY s.id DESC
        ");

        if(count($visitas) == 0){
            return ["status" => "0", "message" => "No hay  seguimiento"];
        }else{
            return $visitas;
        }
       
    }

    
    public function listadoMuestras($institucion_id,$asesor_id,$periodo_id){
        $visitas = DB::SELECT("SELECT  s.* FROM seguimiento_muestra s
        WHERE s.institucion_id = '$institucion_id'
        AND s.asesor_id = '$asesor_id'
        AND s.periodo_id = '$periodo_id'
        ORDER BY s.muestra_id DESC
        ");

        if(count($visitas) == 0){
            return ["status" => "0", "message" => "No hay  seguimiento"];
        }else{
            return $visitas;
        }
       
    }

    public function listadoSeguimientoTipo($institucion_id,$asesor_id,$periodo_id,$tipo){
        $visitas = DB::SELECT("SELECT  s.* FROM seguimiento_cliente s
        WHERE s.institucion_id = '$institucion_id'
        AND s.asesor_id = '$asesor_id'
        AND s.periodo_id = '$periodo_id'
        AND s.tipo_seguimiento = '$tipo'
        AND s.estado <> 2
        ORDER BY s.id DESC
        ");

        if(count($visitas) == 0){
            return ["status" => "0", "message" => "No hay  seguimiento"];
        }else{
            return ["status" => "1", "message" => "No hay  seguimiento","datos" => $visitas];
        }
       
    }

    public function listadoSeguimientoMuestra($institucion_id,$asesor_id,$periodo_id){
        $visitas = DB::SELECT("SELECT  s.* FROM seguimiento_muestra s
        WHERE s.institucion_id = '$institucion_id'
        AND s.asesor_id = '$asesor_id'
        AND s.periodo_id = '$periodo_id'
        ORDER BY s.muestra_id DESC
        ");

        if(count($visitas) == 0){
            return ["status" => "0", "message" => "No hay  seguimiento"];
        }else{
            return ["status" => "1", "message" => "No hay  seguimiento","datos" => $visitas];
        }
       
    }


 
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
}
