<?php

namespace App\Http\Controllers;

use App\Models\RegimenesFiscale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegimenesFiscaleController extends Controller
{
    public function index(){
        
        $regimenes = RegimenesFiscale::all();

            return view('posiciones.regimenes.index', ['regimenes' => $regimenes]);

    }

    public function store(Request $request)
    {
        $validacion = $request->validate([
            'nombre' => 'required|string|max:225|unique:regimenes_fiscales',
            'id_sat' => 'required|integer|unique:regimenes_fiscales',
        ]);
        
        $regimen = RegimenesFiscale::create(['id_sat' => $request->id_sat,'nombre' => $request->nombre]);

        $inf = 'El regimen se agregó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('regimenes')->with('message',$inf);
    }

    public function update(Request $request)
    {
        $validacion = $request->validate([
            'nombre' => 'required|string|max:225|unique:regimenes_fiscales',
            'id_sat' => 'required|integer|unique:regimenes_fiscales',
        ]);

        $regimen = DB::table('regimenes_fiscales')
            ->where('id','=',$request->id)
            ->update([
            'id_sat'=> $request->id_sat_e,
            'nombre'=> $request->nombre_e,
        ]);

        $inf = 'El regimen se modificó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('regimenes')->with('message',$inf);
    }
}
