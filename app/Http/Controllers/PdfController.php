<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PdfController extends Controller
{
     public function cotizacion($id){
        
        $proyecto = DB::table('proyectos')
        ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
        ->select('proyectos.*','estados_proyectos.nombre as estado')
        ->where('proyectos.id','=',$id)->first();

        $cliente = Cliente::where('id','=',$proyecto->cliente_id)->first();
        
        /*$productos =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->groupBy('productos.id', 'productos.nombre','tipos_productos.nombre')
        ->orderBy('productos.nombre','asc')
        ->get();

        $lineas =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.id as sucursal_id','sucursals.domicilio as domicilio','sucursals.superficie','sucursals.marca',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id',
        'productos.id as producto_id', 'productos.nombre as producto','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->orderBy('sucursals.marca','asc')
        ->orderBy('sucursals.id','asc')
        ->orderBy('productos.nombre','asc')
        ->get();*/
        
        $lineas =DB::table('proyecto_lineas')
        ->join('proyectos', 'proyectos.id', '=', 'proyecto_lineas.proyecto_id')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftjoin('productos', 'productos.id', '=', 'proyecto_lineas.producto_id')
        ->leftJoin('estatus_linea_clientes', 'estatus_linea_clientes.id', '=', 'proyecto_lineas.estatus_linea_cliente_id')
        ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.id as sucursal_id','sucursals.domicilio as domicilio','sucursals.superficie','sucursals.marca',
        'municipio_contactos.nombre as municipio', 'estado_contactos.alias as estado', 'pais_contactos.alias as pais','proyectos.id as proyecto_id','sucursals.id_interno',
        'productos.id as producto_id', 'productos.nombre as producto','estatus_linea_clientes.nombre as estatus',
        'tipos_productos.nombre as tipo')
        ->where('proyectos.id','=',$id)
        ->orderBy('sucursals.marca','asc')
        ->orderBy('sucursals.id','asc')
        ->orderBy('productos.nombre','asc')
        ->get();

        if($proyecto->estado == 'Cotización'){
            $tipo = 'Cotización';
        }
        else{
            $tipo = 'Proyecto';
        }
        
        $data = [
            'titulo' => $tipo,
            'fecha' => date('d/m/Y'),
            'lineas' => $lineas,
            'cliente' => $cliente,
            'proyecto' => $proyecto,'
            id' => $id, 
            'productos' => 1
        ];

        //$pdf = Pdf::loadView('proyecto.cotizacionpdf', $data);
        //$pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')
        //return $pdf->download('cotizacion.pdf');

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('proyecto.cotizacionpdf3', $data)->setWarnings(false);
        return $pdf->stream();
    }

    public function ordencompra($id){
        
        $presupuesto =DB::table('presupuestos')
            ->join('proveedors', 'proveedors.id', '=', 'presupuestos.proveedor_id')
            ->leftjoin('estados_presupuestos', 'estados_presupuestos.id', '=', 'presupuestos.estados_presupuesto_id')
            ->leftjoin('fiscal_positions', 'fiscal_positions.id', '=', 'presupuestos.fiscal_position_id')
            ->select('presupuestos.*','proveedors.nombre as proveedor','proveedors.id as proveedor_id','estados_presupuestos.nombre as estado',
            'estados_presupuestos.id as estados_presupuesto_id','fiscal_positions.id as posicion_id','fiscal_positions.nombre as posicion')
            ->where('presupuestos.id','=',$id)
            ->first();
        

        $proveedor = Proveedor::where('id','=',$presupuesto->proveedor_id)->first();
        
        $lineas =DB::table('proyecto_lineas')
        ->join('sucursals', 'sucursals.id', '=', 'proyecto_lineas.sucursal_id')
        ->leftjoin('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
        ->leftjoin('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
        ->leftjoin('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
        ->leftJoin('proveedors', 'proyecto_lineas.proveedor_id', '=', 'proveedors.id')
        ->leftJoin('productos', 'proyecto_lineas.producto_id', '=', 'productos.id')
        ->join('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
        ->select('proyecto_lineas.*','sucursals.nombre as sucursal','sucursals.domicilio as domicilio',
        'municipio_contactos.nombre as municipio', 'estado_contactos.nombre as estado', 'pais_contactos.alias as pais','sucursals.marca',
        'proveedors.id as proveedor_id','proveedors.nombre as proveedor','productos.id as producto_id', 'productos.nombre as producto','tipos_productos.nombre as tipo')
        ->where('proyecto_lineas.presupuesto_id','=',$id)
        ->where('proyecto_lineas.proveedor_id','=',$presupuesto->proveedor_id)
        ->orderBy('sucursals.nombre')
        ->get();

        if($presupuesto->estado == 'Cotización'){
            $tipo = 'Orden de Compra';
            $status = 'Cotización';
        }
        else{
            $tipo = 'Presupuesto';
            $status = 'Autorizado';
        }
        
        $data = [
            'titulo' => $tipo,
            'fecha' => date('d/m/Y'),
            'lineas' => $lineas,
            'proveedor' => $proveedor,
            'presupuesto' => $presupuesto,'
            id' => $id, 
            'productos' => 1,
            'status' => $status,
        ];

        //$pdf = Pdf::loadView('proyecto.cotizacionpdf', $data);
        //$pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->save('myfile.pdf')
        //return $pdf->download('cotizacion.pdf');

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('presupuesto.ordencomprapdf', $data)->setWarnings(false);
        return $pdf->stream();
    }
}
