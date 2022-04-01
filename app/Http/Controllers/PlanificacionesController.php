<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Planificacion;

class PlanificacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $planificaciones = Planificacion::where('Estado_idEstado','=','1')->join('asignatura','asignatura.idasignatura','=','asignatura_idasignatura')->orderBy('idplanificacion', 'DESC')->get();
        return $planificaciones;
    }

    public function setPlanificacion(Request $request){
        if(!empty($request->idplanificacion)){
            $file = $request->file('archivo');
            //RUTA LINUX
            $ruta = '/var/www/vhosts/prolipadigital.com.ec/httpdocs/software/PlataformaProlipa/public/upload/planificacion';
            //RUTA WINDOWS
            //$ruta=public_path();
            $name = $file->getClientOriginalName();
            $url = uniqid().'.'.$file->getClientOriginalExtension();
            $ext = $file->getClientOriginalExtension();
            $file->move($ruta,$url);
            $planificacion = Planificacion::find($request->idplanificacion)->update(
                [
                    'webplanificacion' => $url,
                ]
            );

            return [
                'idplanificacion' => $request->idplanificacion,
                'nombre' => $name,
                'url' => $url,
                'file_ext' => $ext
            ];
            
        }else{
            $file = $request->file('archivo');
            //RUTA LINUX
            $ruta = '/var/www/vhosts/prolipadigital.com.ec/httpdocs/software/PlataformaProlipa/public/upload/planificacion';
            //RUTA WINDOWS
            // $ruta=public_path();
            $name = $file->getClientOriginalName();
            $url = uniqid().'.'.$file->getClientOriginalExtension();
            $ext = $file->getClientOriginalExtension();
            $file->move($ruta,$url);
            $planificacion = new Planificacion();
            $planificacion->webplanificacion = $url;
            $planificacion->save();
            return [
                'idplanificacion' => $planificacion->idplanificacion,
                'nombre' => $name,
                'url' => $url,
                'file_ext' => $ext
            ];
        }
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
        $planificacion = Planificacion::find($request->idplanificacion)->update(
            $request->all()
        );
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
