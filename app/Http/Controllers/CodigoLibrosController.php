<?php

namespace App\Http\Controllers;

use App\Models\CodigoLibros;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Imports\CodigosImport;
use App\Models\HistoricoCodigos;
use Maatwebsite\Excel\Facades\Excel;
use PDO;

class CodigoLibrosController extends Controller
{
   //api:post//codigos/importar
    public function importar(Request $request) 
    {   
       $codigos = json_decode($request->data_codigos);   
        $data = [];
        $codigosLeidos = [];
        $codigosSinBarra = [];
        $codigosBloqueados = [];
        foreach($codigos as $key => $item){
            $institucion = $item->Id_institucion;
            $venta_estado = $item->venta_estado;
            $periodo = $this->PeriodoInstitucion($institucion);
            $traerPeriodo = $periodo[0]->periodo;
            $todate  = date('Y-m-d H:i:s');   

            //para traer los codigos del excel que ya han sido leidos
            $traerCodigosLeidos = DB::SELECT("SELECT c.codigo 
            FROM codigoslibros c
            WHERE codigo = '$item->codigo'
            AND bc_estado = '2'
            AND estado <> '2'
            ");
            //para traer los codigos del excel que ya han sido Pagados
            $traerCodigosPagados = DB::SELECT("SELECT c.codigo 
            FROM codigoslibros c
            WHERE codigo = '$item->codigo'
            AND bc_estado = '3'
            AND estado <> '2'
            ");

            //para traer los codigos del excel que vienen sin barra
            $traerCodigosSinBarra = DB::SELECT("SELECT c.codigo 
            FROM codigoslibros c
            WHERE codigo = '$item->codigo'
            AND bc_estado = '0'
            AND estado <> '2'
            ");
            
            //para traer los codigos bloqueados que vienen desde el excel
            $traerCodigosBloqueados = DB::SELECT("SELECT c.codigo 
            FROM codigoslibros c
            WHERE codigo = '$item->codigo'
            AND estado = '2'
            ");
            

             //para traer los codigos del excel que vienen ya leidos en el sistema
            if(count($traerCodigosLeidos) > 0){
                $codigosLeidos[] =[
                  "codigos" =>   $traerCodigosLeidos[0]->codigo
                ];
            }
             //para traer los codigos del excel que vienen sin barra
            if(count($traerCodigosSinBarra) > 0){
                $codigosSinBarra[] =[
                   "codigos" =>  $traerCodigosSinBarra[0]->codigo
                ];
            }
             //para traer los codigos bloqueados que vienen desde el excel
            if(count($traerCodigosBloqueados) > 0){
                $codigosBloqueados[] =[
                   "codigos" =>  $traerCodigosBloqueados[0]->codigo
                ];
            }
            //para traer los codigos bloqueados que vienen desde el excel
              if(count($traerCodigosPagados) > 0){
                $codigosPagados[] =[
                   "codigos" =>  $traerCodigosPagados[0]->codigo
                ];
            }
            //hace el update a la tabla codigos libros, si el estado es 1 = codigos de barras
           $ingresar =  DB::table('codigoslibros')
            ->where('codigo', $item->codigo)
            ->where('bc_estado', '1')
            ->where('estado','<>'. '2')
            
            ->update([
                'bc_institucion' => $institucion,
                'bc_estado' => 2,
                'bc_periodo' => $traerPeriodo,
                'bc_fecha_ingreso' => $todate,
                'venta_estado' => $venta_estado
            ]);

            //si se actualiza ingresa al historico
            if($ingresar){
                     //ingresar en el historico
                $historico = new HistoricoCodigos();
                $historico->codigo_libro   =  $item->codigo;
                $historico->usuario_editor = $institucion;
                $historico->idInstitucion = $request->id_usuario;
                $historico->id_periodo = $traerPeriodo;
                $historico->observacion = $item->comentario;
                $historico->b_estado = "1";
                $historico->save();
            }
       
        }

        $data = [
            "codigosLeidos" => $codigosLeidos,
            "codigosSinBarra" => $codigosSinBarra,
            "codigosBloqueados" => $codigosBloqueados,
            "codigosPagados" => $codigosPagados
        ];
        return $data;
    }

     //api:post//codigos/bloquear
     public function bloquearCodigos(Request $request) 
     {   
         $codigos = json_decode($request->data_codigos);  
         foreach($codigos as $key => $item){
             $bc_estado = $item->bc_estado;
             //traer usuario quemado
             $encontrarUsuarioQuemado = DB::select("SELECT idusuario, institucion_idInstitucion
             FROM usuario
             WHERE email = 'quemarcodigos@prolipa.com'
             AND id_group = '4'
              
             ");

            //almacenar usuario quemado
            $usuarioQuemado = $encontrarUsuarioQuemado[0]->idusuario;
            //almacenar  institucion del usuario quemado
            $usuarioQuemadoInstitucion = $encontrarUsuarioQuemado[0]->institucion_idInstitucion;
            //periodo del usuario quemado
             $periodo = $this->PeriodoInstitucion($usuarioQuemadoInstitucion);

             $traerPeriodo = $periodo[0]->periodo;
      
             //hace el update a la tabla codigos libros, donde el estado sera a 2 y el bc_estado el que envie por excel
             //y colocar los del usuario quemado
            $ingresar =  DB::table('codigoslibros')
             ->where('codigo', $item->codigo)
             ->update([
                 'bc_estado' => $bc_estado,
                 'estado' => "2",
                 'idusuario' => $usuarioQuemado,
                 'id_periodo' => $traerPeriodo
             ]);

            //ingresar en el historico
            $historico = new HistoricoCodigos();
            $historico->id_usuario   =  $usuarioQuemado;
            $historico->codigo_libro   =  $item->codigo;
            $historico->usuario_editor = $usuarioQuemadoInstitucion;
            $historico->idInstitucion = $request->id_usuario;
            $historico->id_periodo = $traerPeriodo;
            $historico->observacion = $item->comentario;
            $historico->b_estado = "0";
            $historico->save();
         }
         return ["status" => "1" , "message" => "Se bloqueo los codigos correctamente"]; 
     }

     //api:get>>/codigos/revision
     public function revision(Request $request){
        $codigos = json_decode($request->data_codigos);  
        $datos=[];
        $data=[];
        foreach($codigos as $key => $item){
            $consulta = DB::SELECT("SELECT c.codigo, u.institucion_idInstitucion , i.nombreInstitucion, c.updated_at ,c.id_periodo,
            CONCAT(u.nombres, ' ', u.apellidos) as estudiante, u.cedula, u.email, p.periodoescolar as periodo
            FROM codigoslibros c 
            LEFT JOIN usuario u ON c.idusuario = u.idusuario
            LEFT JOIN institucion i ON u.institucion_idInstitucion = i.idInstitucion
            LEFT JOIN periodoescolar p ON c.id_periodo = p.idperiodoescolar
            WHERE c.codigo  ='$item->codigo'
            ");

            if(count($consulta) > 0){
               $datos[$key] = $consulta[0];
            }       
        }

        $data = [
            "informacion" => $datos
        ];
        return $data;

     }
    public function PeriodoInstitucion($institucion){
        $periodoInstitucion = DB::SELECT("SELECT idperiodoescolar AS periodo , periodoescolar AS descripcion FROM periodoescolar WHERE idperiodoescolar = ( 
            SELECT  pir.periodoescolar_idperiodoescolar as id_periodo
            from institucion i,  periodoescolar_has_institucion pir         
            WHERE i.idInstitucion = pir.institucion_idInstitucion
            AND pir.id = (SELECT MAX(phi.id) AS periodo_maximo FROM periodoescolar_has_institucion phi
            WHERE phi.institucion_idInstitucion = i.idInstitucion
            AND i.idInstitucion = '$institucion'))

            
        ");
        return $periodoInstitucion;
    }

    public function index(Request $request)
    {
        $libros = DB::SELECT("SELECT * FROM codigoslibros join libro on libro.idlibro = codigoslibros.libro_idlibro  WHERE codigoslibros.idusuario = ?",[$request->idusuario]);
        return $libros;
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
        $codigo = DB::UPDATE("UPDATE `codigoslibros` SET `idusuario`= ? WHERE `codigo` = ?",[$request->idusuario,"$request->codigo"]);
        return $codigo;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CodigoLibros  $codigoLibros
     * @return \Illuminate\Http\Response
     */
    public function show(CodigoLibros $codigoLibros)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CodigoLibros  $codigoLibros
     * @return \Illuminate\Http\Response
     */
    public function edit(CodigoLibros $codigoLibros)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CodigoLibros  $codigoLibros
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CodigoLibros $codigoLibros)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CodigoLibros  $codigoLibros
     * @return \Illuminate\Http\Response
     */
    public function destroy(CodigoLibros $codigoLibros)
    {
        //
    }
}
