<?php

namespace App\Http\Controllers;

use App\Models\AsignaturaDocente;
use Illuminate\Http\Request;
use DB;
use App\Quotation;
class AsignaturaDocenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $usuario = DB::select("CALL `asignaturasDocente` ( $request->idusuario );");
        return $usuario;
    }

    public function asignaturas_crea_docente($id)
    {
        $asignaturas = DB::SELECT("SELECT a.idasignatura, a.nombreasignatura FROM asignatura a, asignaturausuario au WHERE a.idasignatura = au.asignatura_idasignatura AND au.usuario_idusuario = $id AND a.tipo_asignatura = 0 AND a.estado = '1'");
        return $asignaturas;
    }

    
    public function deshabilitarasignatura($id)
    {
        $asignatura = DB::UPDATE("UPDATE asignatura SET estado = '0' WHERE idasignatura = $id");

        return $asignatura;
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
        DB::SELECT("DELETE FROM `asignaturausuario` WHERE usuario_idusuario = ?",[$request->usuario_idusuario]);
        foreach ($request->asignaturas as $key => $post) {
            $asignatura = new AsignaturaDocente();
            $asignatura->usuario_idusuario = $request->usuario_idusuario;
            $asignatura->asignatura_idasignatura = $post;
            $asignatura->save();
        }
    }


    
     public function guardar_asignatura_usuario(Request $request)
    {
        $asignatura = new AsignaturaDocente();
        $asignatura->usuario_idusuario = $request->usuario_idusuario;
        $asignatura->asignatura_idasignatura = $request->asignatura_idasignatura;

        $asignatura->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AsignaturaDocente  $asignaturaDocente
     * @return \Illuminate\Http\Response
     */
    public function show(AsignaturaDocente $asignaturaDocente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AsignaturaDocente  $asignaturaDocente
     * @return \Illuminate\Http\Response
     */
    public function edit(AsignaturaDocente $asignaturaDocente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AsignaturaDocente  $asignaturaDocente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AsignaturaDocente $asignaturaDocente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AsignaturaDocente  $asignaturaDocente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $respuesta=DB::delete('DELETE FROM `asignaturausuario` WHERE  idasiguser = ?',[$request->asignatura_idasignatura]);
        return $respuesta;
    }
    public function asignaturas_x_docente(Request $request)
    {
        $dato = DB::table('asignaturausuario as ausu')
        ->where('ausu.usuario_idusuario','=',$request->idusuario)
        ->leftjoin('asignatura as asig','ausu.asignatura_idasignatura','=','asig.idasignatura')
        ->select('asig.nombreasignatura','asig.idasignatura','asig.area_idarea', 'ausu.usuario_idusuario as user','ausu.asignatura_idasignatura','ausu.idasiguser as idasignado')
        ->get();
        return $dato;
    }
    public function asignar_asignatura_docentes(Request $request)
    {
        $dato = DB::table('asignaturausuario')
        ->where('usuario_idusuario','=',$request->usuario_idusuario)
        ->where('asignatura_idasignatura','=',$request->asignatura_idasignatura)
        ->get();
        if ($dato->count() > 0) {
            return $dato->count();
        }else{
            $asignatura = new AsignaturaDocente();
            $asignatura->usuario_idusuario = $request->usuario_idusuario;
            $asignatura->asignatura_idasignatura = $request->asignatura_idasignatura;            
            $asignatura->save();
            return $asignatura;
        }
    }
    public function eliminaAsignacion($id)
    {
        $data = AsignaturaDocente::find($id);
        $data->delete();
        return $data;
    }
    public function quitarTodasAsignaturasDocente(Request $request)
    {
        $ids = explode(",",$request->idasiguser);
        $data = AsignaturaDocente::destroy($ids);
        return $data;
     
    }
}
