<?php

namespace App\Http\Controllers;

use App\Models\Area;
use DB;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return csrf_token();
        $area = DB::SELECT("SELECT a.*, t.nombretipoarea
         FROM area a ,tipoareas t
         where  a.tipoareas_idtipoarea  = t.idtipoarea 
         and a.estado = '1'
        ORDER  BY idarea DESC
        ");

        $tipoArea = DB::SELECT("SELECT tipoareas.* FROM tipoareas");
        return["area" => $area, "tipoArea" => $tipoArea];
    }

    public function select()
    {
        $area = Area::all();
        foreach ($area as $key => $post) {
            $respuesta = DB::SELECT("SELECT idasignatura as id, nombreasignatura as name FROM asignatura join area on area.idarea = asignatura.area_idarea WHERE area_idarea = ? ",[$post->idarea]);
            $data['items'][$key] = [
                'id' => "a".$post->idarea,
                'name' => $post->nombrearea,
                'children'=>$respuesta,
            ];
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
        
       if($request->idarea){
       
        $area = Area::findOrFail($request->idarea);
        $area->nombrearea = $request->nombrearea;
        $area->tipoareas_idtipoarea = $request->idtipoarea;

       }else{
           $area = new Area;
           $area->nombrearea = $request->nombrearea;
           $area->tipoareas_idtipoarea = $request->idtipoarea;
       }
       $area->save();
       if($area){
           return "Se guardo correctamente";
       }else{
           return "No se pudo guardar/actualizar";
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Area $area)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(Area $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Area $area)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function areaeliminar(Request $request)
    {
        DB::table('area')
        ->where('idarea', $request->idarea)
        ->update(['estado' => '0']);

    }
}
