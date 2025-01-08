<?php

namespace App\Http\Controllers;

use App\Models\Importacion;
use App\Models\ImportacionProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportacionProyectoController extends Controller
{
    public function index(){
        
        $imports = ImportacionProyecto::all();
       
        return view('importacion.index', ['imports' => $imports]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $import = new ImportacionProyecto();

        $import->nombre = $request->nombre;
        $import->descripcion = $request->descrip;

        $import->save();
        $inf = 1;

        $id = $import->id;

        session()->flash('Exito','Agrega productos a la importación...');
        return redirect()->route('importaciones.productos',['id' => $id])->with('info',$inf);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show($idc, $idl)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = [
            'nombre' => $request->enombre,
            'descripcion' => $request->edescrip,
        ];

        $import = DB::table('importacion_proyectos')
            ->where('id','=',$request->eid)
            ->update($data);

        $inf = 1;

        session()->flash('Exito','Se modificó la importación...');
        return redirect()->route('importaciones')->with('info',$inf);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
}
