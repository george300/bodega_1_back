<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use DB;
use App\Quotation;
class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/') ;
        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $idusuario = auth()->user()->idusuario;
        $idInstitucion = auth()->user()->institucion_idInstitucion;
        if($idInstitucion == 66){
            $videos = DB::select("SELECT * FROM video INNER JOIN asignatura ON video.asignatura_idasignatura = asignatura.idasignatura");
        }else{
            $videos = DB::select('CALL datosvideosd(?)',[$idusuario]);
        }
        return $videos;
    }

    // public function aplicativo(Request $request)
    // {
    //     $videos = Video::join('asignaturausuario','video.asignatura_idasignatura','=','asignaturausuario.asignatura_idasignatura')
    //     ->join('asignatura','asignatura.idasignatura','=','asignaturausuario.asignatura_idasignatura')
    //     ->where('asignaturausuario.usuario_idusuario', '=',$request->idusuario)->paginate(3);
    //     return [
    //         'pagination' => [
    //             'current_page' => $videos->currentPage(),
    //             'per_page'     => $videos->perPage(),
    //             'last_page'    => $videos->lastPage(),
    //             'from'         => $videos->firstItem(),
    //             'to'           => $videos->lastItem(),
    //         ],
    //         'videos' => $videos
    //     ];
    // }

    public function aplicativo(Request $request)
    {
        $videos = DB::select('CALL datosvideosd(?)',[$request->idusuario]);
        return $videos;
    }


    public function video(Request $request)
    {
        $video = DB::select('SELECT * FROM video');
        return $video;
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
        Video::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        //
    }


    
    public function videos_libro_unidad($id)
    {
        $videos = DB::SELECT("SELECT v . * FROM video v, temas t WHERE v.id_tema = t.id AND t.id_unidad = $id");
        
        return $videos;
    }

    public function videos_libro_tema($id)
    {
        $videos = DB::SELECT("SELECT * FROM video WHERE id_tema = $id");
        
        return $videos;
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $respuesta=DB::update('UPDATE video SET nombrevideo = ? ,descripcionvideo = ? ,webvideo = ? ,Estado_idEstado = ? ,asignatura_idasignatura = ?   WHERE idvideo = ?',[$request->nombrevideo,$request->descripcionvideo,$request->webvideo,$request->Estado_idEstado,$request->asignatura_idasignatura,$request->idvideo]);
        return $respuesta;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        DB::delete('DELETE FROM video WHERE idvideo = ?',[$request->idvideo]);
    }
}
