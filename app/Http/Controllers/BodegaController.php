<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App;
use App\Models\CodigosLibros;
use Illuminate\Support\Facades\DB;

class BodegaController extends Controller
{
  
    public function index(Request $request)
    {
      
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
       
        $codigos = json_decode($request->data_codigos); 
        

        $repetidos = [];
        foreach($codigos as $key => $item){
            $validar = DB::select("SELECT * FROM codigoslibros
            where codigo  = '$item->codigo'");

            if(count($validar) >0){
                $repetidos[$key] = [
                    "codigos" =>  $item->codigo,
                    "repetidas" => count($validar)
                ] ;
            }
            
            $codigo = new CodigosLibros;
            $codigo->codigo = $item->codigo;
            $codigo->observacion = $request->observacion;
   
            $codigo->save();
        }
        return ["codigosRepetidos" => $repetidos];



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
