<?php

namespace App\Http\Controllers;

use App\Models\EstatusLineaCliente;
use App\Models\MovimientosPagoCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovimientosPagoClienteController extends Controller
{
    public function index($id){
        
       $termino = DB::table('terminos_pago_clientes')
            ->where('id',$id)
            ->first();

        $estatus = EstatusLineaCliente::all();
        
        $movimientos = DB::table('movimientos_pago_clientes')
            ->leftJoin('terminos_pago_clientes', 'terminos_pago_clientes.id', '=', 'movimientos_pago_clientes.terminos_pago_cliente_id')
            ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'movimientos_pago_clientes.estatus_linea_cliente_id')
            ->select('movimientos_pago_clientes.*','terminos_pago_clientes.id as termino_id','terminos_pago_clientes.nombre as termino'
            ,'estatus_linea_clientes.id as estatus_id','estatus_linea_clientes.nombre as estatus')
            ->where('terminos_pago_clientes.id',$id)
            ->orderBy('movimientos_pago_clientes.secuencia')
            ->get();
       
        return view('terminoscliente.movimientos.index', ['estatus' => $estatus,'termino' => $termino, 'movimientos' => $movimientos, 'id' => $id]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($idp)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        
        $data = MovimientosPagoCliente::latest('secuencia')->where('terminos_pago_cliente_id',$id)->first();
        if( $data){
            $secuencia = $data->secuencia + 1;
        }
        else{
            $secuencia = 1;
        }

        $movimiento = new MovimientosPagoCliente();

        $movimiento->terminos_pago_cliente_id = $id;
        $movimiento->nombre = $request->nombre;
        $movimiento->secuencia = $secuencia;
        $movimiento->estatus_linea_cliente_id = $request->estatus;
        if  ($request->facturable){
            $movimiento->porcentaje = $request->porcentaje;
        }
        $movimiento->facturable = $request->facturable;

        $movimiento->save();
        $inf = 1;

        session()->flash('Exito','Se agrega con éxito el movimiento al termino de pago...');
        return redirect()->route('termclie.movimientos',['id' => $id])->with('info',$inf);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $termino = DB::table('terminos_pago_clientes')
            ->where('id',$id)
            ->first();

        $estatus = EstatusLineaCliente::all();
        
        $movimientos = DB::table('movimientos_pago_clientes')
            ->leftJoin('terminos_pago_clientes', 'terminos_pago_clientes.id', '=', 'movimientos_pago_clientes.terminos_pago_cliente_id')
            ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'movimientos_pago_clientes.estatus_linea_cliente_id')
            ->select('movimientos_pago_clientes.*','terminos_pago_clientes.id as termino_id','terminos_pago_clientes.nombre as termino'
            ,'estatus_linea_clientes.id as estatus_id','estatus_linea_clientes.nombre as estatus')
            ->where('terminos_pago_clientes.id',$id)
            ->orderBy('movimientos_pago_clientes.secuencia')
            ->get();
       
        return view('terminoscliente.movimientos.show', ['estatus' => $estatus,'termino' => $termino, 'movimientos' => $movimientos, 'id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\top50  $top50
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $termino = DB::table('terminos_pago_clientes')
            ->where('id',$id)
            ->first();

        $estatus = EstatusLineaCliente::all();
        
        $movimientos = DB::table('movimientos_pago_clientes')
            ->leftJoin('terminos_pago_clientes', 'terminos_pago_clientes.id', '=', 'movimientos_pago_clientes.terminos_pago_cliente_id')
            ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'movimientos_pago_clientes.estatus_linea_cliente_id')
            ->select('movimientos_pago_clientes.*','terminos_pago_clientes.id as termino_id','terminos_pago_clientes.nombre as termino'
            ,'estatus_linea_clientes.id as estatus_id','estatus_linea_clientes.nombre as estatus')
            ->where('terminos_pago_clientes.id',$id)
            ->orderBy('movimientos_pago_clientes.secuencia')
            ->get();
       
        return view('terminoscliente.movimientos.edit', ['estatus' => $estatus,'termino' => $termino, 'movimientos' => $movimientos, 'id' => $id]);
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
