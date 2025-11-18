<?php

namespace App\Http\Controllers;

use App\Models\FiscalPosition;
use App\Models\RegimenesFiscale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FiscalPositionController extends Controller
{
    public function index(){
        
        $posiciones =  DB::table('fiscal_positions')
                ->join('regimenes_fiscales', 'regimenes_fiscales.id', '=', 'fiscal_positions.regimenes_fiscale_id')
                ->select('fiscal_positions.*', 'regimenes_fiscales.nombre as regimen', 'regimenes_fiscales.id as regimen_id')
                ->get();
        $regimenes = RegimenesFiscale::all();

        return view('posiciones.index', ['posiciones' => $posiciones,'regimenes' => $regimenes]);

    }

    public function store(Request $request)
    {
        $validacion = $request->validate([
            'nombre' => 'required|string|max:225|unique:fiscaL_positions',
            'iva_t' => 'required',
            'isr_r' => 'required',
            'iva_r' => 'required',
            'imp_c' => 'required',
        ]);
        
        
        $posicion = FiscalPosition::create([
            'nombre' => $request->nombre,
            'regimenes_fiscale_id' => $request->regimen,
            'iva_t' => $request->iva_t,
            'isr_r' => $request->isr_r,
            'iva_r' => $request->iva_r,
            'imp_c' => $request->imp_c,
        ]);

        $inf = 'La posicion fiscal se agregó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('posiciones')->with('message',$inf);
    }

    public function update(Request $request)
    {
        $validacion = $request->validate([
            'nombre_e' => 'required|string|max:225|unique:fislcaL_positions',
            'iva_t_e' => 'required',
            'isr_r_e' => 'required',
            'iva_r_e' => 'required',
            'imp_c_e' => 'required',
        ]);

        $posicion = DB::table('fiscal_positions')
            ->where('id','=',$request->id)
            ->update([
            'nombre' => $request->nombre_e,
            'regimenes_fiscale_id' => $request->regimen_e,
            'iva_t' => $request->iva_t_e,
            'isr_r' => $request->isr_r_e,
            'iva_r' => $request->iva_r_e,
            'imp_c' => $request->imp_c_e,
        ]);

        $inf = 'La posicion fiscal se modificó con éxito...';
        session()->flash('Exito',$inf);
        return redirect()->route('posiciones')->with('message',$inf);
    }
}
