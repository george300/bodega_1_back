<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\CodigosLibros;
use App\Models\Ciudad;
use App\Models\Institucion;
use App\Models\Usuario;
use App\Models\Periodo;
use DataTables;
Use Exception;
use App\Models\CodigosObservacion;
use GraphQL\Server\RequestError;

class CodigosLibrosGenerarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $codigos_libros = DB::SELECT("SELECT * from codigoslibros limit 100");
        return $codigos_libros;
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

    public function makeid($longitud){
        $characters = 'ABCDEFGHKMNPRSTUVWXYZ23456789';
        // $characters = 'AB';
        $charactersLength = strlen($characters);
   
        $randomString = '';
        for ($i = 0; $i < 5; $i++) {
            for ($i = 0; $i < $longitud; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
         }   
    }
    public function makeid2($longitud){
        $characters = 'ABCDEFGHKMNPRSTUVWXYZ23456789';
      
        $charactersLength = strlen($characters);
   
        $randomString = '';
        for ($i = 0; $i < 5; $i++) {
            for ($i = 0; $i < $longitud; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
         }   
    }
    public function makeid3($longitud){
        $characters = 'ABCDEFGHKMNPRSTUVWXYZ23456789';
      
        $charactersLength = strlen($characters);
   
        $randomString = '';
        for ($i = 0; $i < 5; $i++) {
            for ($i = 0; $i < $longitud; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
         }   
    }


    public function generarCodigos(Request $request){
        $longitud  = $request->longitud;
        $code = $request->code;
        $cantidad = $request->cantidad;

        $codigos = [];
        for ($i = 0; $i < $cantidad; $i++) {

            $caracter = $this->makeid($longitud);
            $codigo = $code.$caracter;

            //primera validacion
            $validar = DB::SELECT("SELECT codigo from codigoslibros WHERE codigo = '$codigo'");
            //si el codigo existe se procede a generar una segunda vez el codigo
            if($validar){
                //segunda validacion
                $caracter2 = $this->makeid2($longitud);
                $codigo2 = $code.$caracter2;
                //tercera validacion
                    $validar2 = DB::SELECT("SELECT codigo from codigoslibros WHERE codigo = '$codigo2'");
                  
                    if($validar2){
                        $caracter3 = $this->makeid3($longitud);
                        $codigo3 = $code.$caracter3;
                        $codigos[$i] = ["codigo" => $codigo3];
                    }else{
                        $codigos[$i] = ["codigo" => $codigo2];
                    }
            //si el codigo no existe se agrega al array
            }else{
                $codigos[$i] = ["codigo" => $codigo];
            }
           
        }
       
        return ["codigos" => $codigos];
    }

  
    public function store(Request $request)
    {
        set_time_limit(6000);
        ini_set('max_execution_time', 6000);
		
        $porcentaje = 0;

        $codigos = explode(",", $request->codigo);
        $tam = sizeof($codigos);

        $codigosError = [];
        for( $i=0; $i<$tam; $i++ ){
            $codigos_libros = new CodigosLibros();

            $codigos_libros->serie = $request->serie;
            $codigos_libros->libro = $request->libro;
            $codigos_libros->anio = $request->anio;
            $codigos_libros->libro_idlibro = $request->idlibro;
            $codigos_libros->estado = $request->estado;
			$codigos_libros->idusuario = 0;
            $codigos_libros->bc_estado = 1;
            $codigos_libros->idusuario_creador_codigo = $request->idusuario;

            $codigo_verificar = $codigos[$i];
            $verificar_codigo = DB::SELECT("SELECT codigo from codigoslibros WHERE codigo = '$codigo_verificar'");

            if( $verificar_codigo ){
                $codigoNoIngresado = $codigos[$i];
                $codigosError[$i] = [
                    "codigos" => $codigoNoIngresado
                ];
            }else{
                $codigos_libros->codigo = $codigos[$i];
                $codigos_libros->save();
                $porcentaje++;
            }

        }

        return ["porcentaje" =>$porcentaje ,"codigosNoIngresados" => $codigosError] ;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function editarCodigoBuscado($datos)
    {
        
        $data = explode("*", $datos);
        $codigo = $data[0];
        $libro = $data[1];
        $serie = $data[2];
        $anio = $data[3];
        $idusuario_creador_codigo = $data[4];
        $idusuario = $data[5];
        $idLibro = $data[6];
        $id_periodo = $data[7];
        $id_institucion = $data[8];

        if( $codigo != "" ){

            $has_periodo = DB::SELECT("SELECT `id_periodo` FROM `codigoslibros` WHERE `codigo` = '$codigo'");
            if( $has_periodo[0]->id_periodo == null ){
                $codigos_libros = DB::UPDATE("UPDATE `codigoslibros` SET `codigo`='$codigo',`serie`='$serie',`libro`='$libro',`anio`='$anio',`idusuario_creador_codigo`=$idusuario_creador_codigo,`idusuario`='$idusuario', `libro_idlibro`=$idLibro,`id_periodo` = $id_periodo WHERE `codigo`= '$codigo'");
            }else{
                $codigos_libros = DB::UPDATE("UPDATE `codigoslibros` SET `codigo`='$codigo',`serie`='$serie',`libro`='$libro',`anio`='$anio',`idusuario_creador_codigo`=$idusuario_creador_codigo,`idusuario`='$idusuario', `libro_idlibro`=$idLibro WHERE `codigo`= '$codigo'");
            }
            
            
            DB::INSERT("INSERT INTO hist_codlibros(id_usuario, codigo_libro,usuario_editor, idInstitucion,  observacion,id_periodo) VALUES ($idusuario, '$codigo', $id_institucion, $idusuario_creador_codigo, 'modificado', $id_periodo)");

            return $codigos_libros;

        }else{

            return 'Codigo no encontrado';

        }

    }


    public function show($id)
    {
        $codigos_libros = DB::SELECT("SELECT * from codigoslibros WHERE libro = '$id'");
        return $codigos_libros;
    }


    
    public function codigosLibrosFecha($datos)
    {   
        $data = explode("*", $datos);

        if( $data[0] != "" ){
            $datalibro = explode("-", $data[0]);
            $fecha = $data[1];

            $libro = $datalibro[1];
            $serie = $datalibro[0];
            //SELECT c.idusuario, c.codigo, l.nombrelibro as libro, c.serie, c.anio, c.fecha_create, s.id_serie, s.nombre_serie, c.libro_idlibro, u.nombres, u.apellidos, u.cedula, i.nombreInstitucion from codigoslibros c, series s, libro l, usuario u, institucion i WHERE c.serie = s.nombre_serie AND c.libro_idlibro = l.idlibro AND c.libro = '$libro' AND c.serie = '$serie' AND c.created_at like '$fecha%' AND c.idusuario = u.idusuario AND u.institucion_idInstitucion = i.idInstitucion
            $codigos_libros = DB::SELECT("SELECT c.idusuario, c.codigo, l.nombrelibro as libro, c.serie, c.anio, c.fecha_create, s.id_serie, s.nombre_serie, c.libro_idlibro, u.nombres, u.apellidos, u.cedula, i.nombreInstitucion FROM codigoslibros c INNER JOIN series s ON c.serie = s.nombre_serie INNER JOIN libro l ON c.libro_idlibro = l.idlibro LEFT JOIN usuario u ON c.idusuario = u.idusuario LEFT JOIN institucion i ON u.institucion_idInstitucion = i.idInstitucion WHERE c.libro = '$libro' AND c.serie = '$serie' AND c.created_at like '$fecha%'");
            
            return $codigos_libros;
            
        }else{
            return 0;
        }
        
    }

    
    public function codigosLibrosCodigo($codigo)
    {
        $codigos_libros = DB::SELECT("SELECT c.verif1,c.verif2,c.verif3,c.verif4,c.verif5,c.verif6,c.verif7,c.verif8,c.verif9,c.verif10,
         c.contrato,c.codigo, c.serie, l.nombrelibro as libro, c.anio, c.idusuario, c.idusuario_creador_codigo, c.libro_idlibro, c.estado, c.fecha_create, c.created_at, c.updated_at, s.id_serie, s.nombre_serie, s.longitud_numeros, s.longitud_letras, u.nombres, u.apellidos, u.email, u.cedula, i.nombreInstitucion , periodoescolar.descripcion as periodo, periodoescolar.idperiodoescolar
        FROM codigoslibros c 
        INNER JOIN series s ON c.serie = s.nombre_serie
        INNER JOIN libro l ON c.libro_idlibro = l.idlibro 
        LEFT JOIN usuario u ON c.idusuario = u.idusuario 
        LEFT JOIN institucion i ON u.institucion_idInstitucion = i.idInstitucion
        LEFT JOIN periodoescolar ON periodoescolar.idperiodoescolar = c.id_periodo
        WHERE c.codigo like '%$codigo%'");
        return $codigos_libros;
    }


    public function librosBuscar(){//select buscar
        //SELECT l.id_libro_serie as id, concat_ws('-', s.nombre_serie, l.nombre) as label from libros_series l, series s WHERE l.id_serie = s.id_serie
        $codigos_libros = DB::SELECT("SELECT l.id_libro_serie as id, concat_ws('-', s.nombre_serie, l.nombre) as label from libros_series l, series s WHERE l.id_serie = s.id_serie");
        return $codigos_libros;
    }




    public function codigosLibrosExportados($data){
        $datos = explode("*", $data);
        $usuario = $datos[0];
        $cantidad = $datos[1];

        $codigos_libros = DB::SELECT("SELECT * from codigoslibros WHERE idusuario_creador_codigo = '$usuario' ORDER BY fecha_create DESC LIMIT $cantidad");

        return $codigos_libros;
    }

    

    public function reportesCodigoInst(Request $request){

        $codigos_libros = DB::SELECT("(SELECT COUNT(c.codigo) as cantidad, GROUP_CONCAT(DISTINCT c.serie) as serie, (SELECT l.nombrelibro FROM libro l WHERE l.idlibro = (GROUP_CONCAT(DISTINCT c.libro_idlibro)) ) as libro, GROUP_CONCAT(DISTINCT (SELECT ciudad.nombre FROM ciudad WHERE ciudad.idciudad = i.ciudad_id) ) as ciudad, GROUP_CONCAT(DISTINCT (SELECT GROUP_CONCAT(usuario.nombres,' ',usuario.apellidos) as vendedor FROM usuario WHERE usuario.cedula = (SELECT institucion.vendedorInstitucion FROM institucion WHERE institucion.idInstitucion = i.idInstitucion ) ) ) as asesor, GROUP_CONCAT(DISTINCT (SELECT i.nombreInstitucion FROM institucion WHERE institucion.idInstitucion = i.idInstitucion ) ) as institucion FROM codigoslibros c, usuario u, institucion i WHERE c.idusuario = u.idusuario AND u.institucion_idInstitucion = i.idInstitucion AND i.idInstitucion = $request->id AND c.updated_at BETWEEN CAST('$request->fromdate' AS DATE) AND CAST('$request->todate' AS DATE) AND c.codigo not like '%plus%' GROUP BY c.libro_idlibro ORDER BY `c`.`updated_at`  DESC) UNION (SELECT COUNT(c.codigo) as cantidad, GROUP_CONCAT(DISTINCT c.serie, ' PLUS') as serie, (SELECT l.nombrelibro FROM libro l WHERE l.idlibro = (GROUP_CONCAT(DISTINCT c.libro_idlibro)) ) as libro, GROUP_CONCAT(DISTINCT (SELECT ciudad.nombre FROM ciudad WHERE ciudad.idciudad = i.ciudad_id) ) as ciudad, GROUP_CONCAT(DISTINCT (SELECT GROUP_CONCAT(usuario.nombres,' ',usuario.apellidos) as vendedor FROM usuario WHERE usuario.cedula = (SELECT institucion.vendedorInstitucion FROM institucion WHERE institucion.idInstitucion = i.idInstitucion ) ) ) as asesor, GROUP_CONCAT(DISTINCT (SELECT i.nombreInstitucion FROM institucion WHERE institucion.idInstitucion = i.idInstitucion ) ) as institucion FROM codigoslibros c, usuario u, institucion i WHERE c.idusuario = u.idusuario AND u.institucion_idInstitucion = i.idInstitucion AND i.idInstitucion = $request->id AND c.updated_at BETWEEN CAST('$request->fromdate' AS DATE) AND CAST('$request->todate' AS DATE) AND c.codigo like '%plus%' GROUP BY c.libro_idlibro ORDER BY `c`.`updated_at`  DESC)");

        return $codigos_libros;
    }


    public function reportesCodigoAsesor($id,$periodo){


        $codigos_libros = DB::SELECT("SELECT codigoslibros.codigo,codigoslibros.estado,  (SELECT l.nombrelibro FROM libro l WHERE l.idlibro = codigoslibros.libro_idlibro) as libro, codigoslibros.updated_at as registrado, usuario.cedula as cedula, usuario.nombres, usuario.apellidos, usuario.email, periodoescolar.descripcion as periodo
        from codigoslibros
        LEFT JOIN periodoescolar ON periodoescolar.idperiodoescolar = codigoslibros.id_periodo
        JOIN usuario on usuario.idusuario = codigoslibros.idusuario 
        WHERE usuario.institucion_idInstitucion = $id
        AND periodoescolar.idperiodoescolar = $periodo
        ");
        return $codigos_libros;
    }

    
    public function seriesCambiar(){
        $codigos_libros = DB::SELECT("SELECT id_serie as id, nombre_serie as label from series");

        return $codigos_libros;
    }

    
    public function librosSerieCambiar($id){
        $codigos_libros = DB::SELECT("SELECT idLibro as id, nombre as label from libros_series WHERE id_serie = $id");

        return $codigos_libros;
    }

    
    public function institucionesResportes(Request $request){

        if($request->filtroInstitucion){
            $instituciones = DB::SELECT("SELECT i.idInstitucion as id, i.region_idregion,i.nombreInstitucion as label, c.nombre as nombre_ciudad, pi.periodoescolar_idperiodoescolar as id_periodo
            from institucion i, ciudad c, periodoescolar_has_institucion pi 
            WHERE i.ciudad_id = c.idciudad
            AND i.idInstitucion = pi.institucion_idInstitucion
            AND i.ciudad_id = $request->ciudad_id
             AND pi.id = (SELECT MAX(phi.id) AS periodo_maximo FROM periodoescolar_has_institucion phi WHERE phi.institucion_idInstitucion = i.idInstitucion)");
        }else{
            $instituciones = DB::SELECT("SELECT i.idInstitucion as id,i.nombreInstitucion as label, c.nombre as nombre_ciudad, pi.periodoescolar_idperiodoescolar as id_periodo
            from institucion i, ciudad c, periodoescolar_has_institucion pi 
            WHERE i.ciudad_id = c.idciudad
            AND i.idInstitucion = pi.institucion_idInstitucion
             AND pi.id = (SELECT MAX(phi.id) AS periodo_maximo FROM periodoescolar_has_institucion phi WHERE phi.institucion_idInstitucion = i.idInstitucion)");
        }
       

        return $instituciones;
    }


    public function editarInstEstud(Request $request){


           ///Para buscar el periodo

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
          
         //fin de busqueda del periodo
            //almancenar el periodo
           $periodo =  $verificarperiodos[0]->idperiodoescolar;
        
       
     $periodos =   DB::table('codigoslibros')
        ->where('codigo', $request->codigo)
        ->update(['id_periodo' => $periodo]);

       

        $institucion = DB::UPDATE("UPDATE usuario SET institucion_idInstitucion=$request->idInstitucion WHERE idusuario=$request->id;");

      

        // DB::UPDATE("UPDATE codigoslibros SET id_periodo=$periodo WHERE codigo=$request->codigo");
        
        return $institucion;
    }



    public function librosCambiar($id){
        $codigos_libros = DB::SELECT("SELECT * from libros_series WHERE idLibro = $id");

        return $codigos_libros;
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
    public function cambioEstadoCodigo(Request $request)
    {        

       $encontrarUsuarioQuemado = DB::select("SELECT idusuario, institucion_idInstitucion
       FROM usuario
       WHERE email = 'quemarcodigos@prolipa.com'
       AND id_group = '4'
        
       ");

         //almacenar usuario institucion
         $usuarioQuemadoInstitucion = $encontrarUsuarioQuemado[0]->institucion_idInstitucion;

        
        $traerPeriodo = $this->institucionTraerPeriodo($usuarioQuemadoInstitucion);
        $periodo = $traerPeriodo[0]->periodo;
    
       if(!empty($encontrarUsuarioQuemado)){


           //almacenar usuario quemado
           $usuarioQuemado = $encontrarUsuarioQuemado[0]->idusuario;
            $cambio = CodigosLibros::find($request->codigo);
            $cambio->estado = $request->estado;
            
            $cambio->save();
            if($cambio){
               $observacion= DB::INSERT("INSERT INTO hist_codlibros(id_usuario, codigo_libro, idInstitucion, usuario_editor, observacion,id_periodo) VALUES ($usuarioQuemado, '$request->codigo', $request->usuario_editor, '66', '$request->observacion', '$periodo')");
              

               //Actualizar la tabla codigos libros
               DB::table('codigoslibros')
                ->where('codigo', $request->codigo)
                ->update([
                    'idusuario' => $usuarioQuemado,
                    'id_periodo' => $periodo
                  
                ]);
            }
            if($observacion){
                return $cambio;
            }
       }else{
           return ["status" => "0", "message" => "No se encontro el usuario quemado quemarcodigos@prolipa.com con id de usuario 45017"];
       }

     
    }

        //api para traer el periodo por institucion
     public function institucionTraerPeriodo($institucion){
            $periodoInstitucion = DB::SELECT("SELECT idperiodoescolar AS periodo , periodoescolar AS descripcion FROM periodoescolar WHERE idperiodoescolar = ( 
                SELECT  pi.periodoescolar_idperiodoescolar as id_periodo
                from institucion i,  periodoescolar_has_institucion pi         
                WHERE i.idInstitucion = pi.institucion_idInstitucion
                AND pi.id = (SELECT MAX(phi.id) AS periodo_maximo FROM periodoescolar_has_institucion phi
                WHERE phi.institucion_idInstitucion = i.idInstitucion
                AND i.idInstitucion = '$institucion'))
            ");
            
            return $periodoInstitucion;
    }

        
    //agregar codigos perdidos, solicitados por soporte
    public function agregar_codigo_perdido(Request $request)
    {
        $agregar = new CodigosLibros();
        $agregar->codigo = $request->codigo;
        $agregar->serie = $request->serie;
        $agregar->libro = $request->libro;
        $agregar->anio = $request->anio;
        $agregar->idusuario =$request->idusuario;
        $agregar->idusuario_creador_codigo = $request->idusuario_creador_codigo;
        $agregar->libro_idlibro = $request->libro_idlibro;
        $agregar->estado = $request->estado;        
        $agregar->save();
        return $agregar;   
    }
//busqueda de codigos de libros registrados y eliminados por los estudiantes
    public function getHistoricoCodigos($id)
    {
        $codigos = DB::SELECT("SELECT his.codigo_libro,cd.id_periodo, p.descripcion, his.observacion,  his.created_at as fecha_observacion, u.cedula, concat(u.nombres, ' ',  u.apellidos) as usuario, u.email, u.estado_idEstado, i.nombreInstitucion, c.nombre as ciudad, cd.serie, cd.libro, cd.anio, cd.estado, cd.fecha_create
        from hist_codlibros his, usuario u, institucion i, ciudad c, codigoslibros cd
        LEFT JOIN periodoescolar p ON cd.id_periodo = p.idperiodoescolar
        WHERE his.id_usuario = u.idusuario
        AND u.institucion_idInstitucion = i.idInstitucion
        AND i.ciudad_id = c.idciudad
        AND his.codigo_libro = cd.codigo
    
        -- AND p.idperiodoescolar  = cd.id_periodo
        AND u.cedula  = '$id'");
        
        return $codigos;
    }

    // metodo para cargar el id del periodo actual del estudiante al cual se le haya asignado cada codigo
    public function cargarPeriodoCodigo()
    {
        set_time_limit(60000);
        ini_set('max_execution_time', 60000);

        $codigos = DB::SELECT("SELECT c.codigo, p.idperiodoescolar FROM codigoslibros c, usuario u, periodoescolar_has_institucion pi, periodoescolar p WHERE c.idusuario IS NOT null AND c.idusuario != 0 AND c.idusuario = u.idusuario AND u.institucion_idInstitucion = pi.institucion_idInstitucion AND pi.periodoescolar_idperiodoescolar = p.idperiodoescolar AND pi.id = (SELECT MAX(phi.id) AS periodo_maximo FROM periodoescolar_has_institucion phi WHERE phi.institucion_idInstitucion = pi.institucion_idInstitucion) AND c.id_periodo IS null");
        

       
        foreach ($codigos as $key => $value) {
            DB::UPDATE("UPDATE `codigoslibros` SET `id_periodo` = ? WHERE `codigo` = ?", [$value->idperiodoescolar, $value->codigo]);
        }
        return ["status"=>"1"];
    }

    //metodo para agregar el periodo a los cursos
    public function agregarPeriodoCurso(){
     
        set_time_limit(60000);
        ini_set('max_execution_time', 60000);

        $cursos = DB::SELECT("SELECT c.idcurso, p.idperiodoescolar
        FROM curso c, usuario u, periodoescolar_has_institucion pi, periodoescolar p 
        WHERE c.idusuario IS NOT null
        AND c.idusuario != 0 
        AND c.idusuario = u.idusuario
        AND u.institucion_idInstitucion = pi.institucion_idInstitucion
        AND pi.periodoescolar_idperiodoescolar = p.idperiodoescolar
        AND pi.id = (SELECT MAX(phi.id) AS periodo_maximo FROM periodoescolar_has_institucion phi WHERE phi.institucion_idInstitucion = pi.institucion_idInstitucion) 
        AND c.id_periodo IS null
        LIMIT 100
        ");

       
        
        foreach ($cursos as $key => $value) {
            DB::UPDATE("UPDATE `curso` SET `id_periodo` = ? WHERE `idcurso` = ?", [$value->idperiodoescolar, $value->idcurso]);
        }
        return ["status"=>"1"];
    }

   


    public function hist_codigos($id)
    {
        $registro = CodigosLibros::select('codigoslibros.serie','codigoslibros.libro','codigoslibros.fecha_create','codigoslibros.updated_at as actualizado','codigoslibros.idusuario', 'usuario.nombres','usuario.apellidos', 'periodoescolar.periodoescolar', 'periodoescolar.descripcion as periododescripcion', 'institucion.nombreInstitucion', 'ciudad.nombre as nombre_ciudad' )
        ->leftjoin('usuario','codigoslibros.idusuario','=','usuario.idusuario')
        ->leftjoin('libro','codigoslibros.libro_idlibro', '=', 'libro.idlibro')
        ->leftjoin('periodoescolar','codigoslibros.id_periodo', '=', 'periodoescolar.idperiodoescolar')
        ->leftjoin('institucion','usuario.institucion_idInstitucion', '=', 'institucion.idInstitucion')
        ->leftjoin('ciudad','institucion.ciudad_id', '=', 'ciudad.idciudad')
        ->where('codigoslibros.codigo', 'LIKE',$id)
        ->get();


        //en el campo id institucion, esta guardado el id del usuario, y en el usuario editor, el id institucion        
        $codigos = DB::SELECT("SELECT i.nombreInstitucion as institucion_historico, ins.nombreInstitucion as institucion_usuario, c.nombre as ciudad,
        CONCAT(u.nombres, ' ' , u.apellidos) as usuario_editor, 
        CONCAT(us.nombres, ' ' , us.apellidos) as usuario, us.email, us.cedula,
        e.nombreestado,
        h.observacion, h.created_at as fecha_registro
        FROM hist_codlibros h
        LEFT JOIN institucion i ON h.usuario_editor = i.idInstitucion
        LEFT JOIN usuario u ON h.idInstitucion = u.idusuario
        LEFT JOIN usuario us ON h.id_usuario = us.idusuario
        LEFT JOIN institucion ins ON us.institucion_idInstitucion = ins.idInstitucion
        LEFT JOIN ciudad c ON ins.ciudad_id = c.idciudad
        LEFT JOIN estado e on us.estado_idEstado = e.idEstado
        WHERE h.codigo_libro  LIKE '$id%'
        ORDER BY h.created_at DESC");
        
        return ['historico'=> $codigos, 'registro'=>$registro];
    }

}
