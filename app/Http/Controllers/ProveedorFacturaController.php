<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorFacturaController extends Controller
{
    public function index(){
        
        $facturas = DB::table('proveedor_facturas')
            ->join('proveedors','proveedors.id','=','proveedor_facturas.proveedor_id')
            ->join('presupuestos','presupuestos.id','=','proveedor_facturas.presupuesto_id')
            ->select('proveedor_facturas.*','proveedors.nombre as proveedor','proveedors.rfc as rfc','presupuestos.nombre as presupuesto')
            ->orderBy('proveedor_facturas.id')
            ->get();

       
        return view('factproveedores.index', ['facturas' => $facturas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $presupuestos = DB::table('presupuestos')
            ->join('proveedors','proveedors.id','=','presupuestos.proveedor_id')
            ->select('presupuestos.*','proveedors.nombre as proveedor','proveedors.rfc as rfc')
            ->where('proveedors.cxp', '>',0)
            ->get();
        
        return view('factproveedores.create', ['presupuestos' => $presupuestos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show(top50 $top50)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit(top50 $top50)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, top50 $top50)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function destroy(top50 $top50)
    {
        //
    }
    
}
