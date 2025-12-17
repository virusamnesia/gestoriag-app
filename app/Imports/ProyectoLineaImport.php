<?php

namespace App\Imports;

use App\Models\ImportacionError;
use App\Models\ProyectoLinea;
use Error;
use Exception;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProyectoLineaImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $importacion = DB::table('importacions')
                ->select('importacions.*')
                ->orderBy('id','desc')
                ->first();
        
        
        foreach ($rows as $row){

            $proyecto = DB::table('proyectos')
                ->leftjoin('estados_proyectos', 'estados_proyectos.id', '=', 'proyectos.estados_proyecto_id')
                ->select('proyectos.*','estados_proyectos.nombre as estado','estados_proyectos.id as estado_id')
                ->where('proyectos.id','=',$importacion->proyecto_id)
                ->orderBy('proyectos.id','desc')
                ->first();

            $sucursal = DB::table('sucursals')
                ->join('clientes', 'clientes.id', '=', 'sucursals.cliente_id')
                ->join('ciudad_contactos', 'ciudad_contactos.id', '=', 'sucursals.ciudad_contacto_id')
                ->join('municipio_contactos', 'municipio_contactos.id', '=', 'sucursals.municipio_contacto_id')
                ->join('estado_contactos', 'estado_contactos.id', '=', 'sucursals.estado_contacto_id')
                ->join('pais_contactos', 'pais_contactos.id', '=', 'sucursals.pais_contacto_id')
                ->select('sucursals.*','clientes.nombre as cliente')
                ->where('sucursals.marca','=',$row['marca'])
                ->where('sucursals.id_interno','=',$row['id'])
                ->where('sucursals.nombre','=',$row['sucursal'])
                ->where('sucursals.cliente_id','=',$importacion->cliente_id)
                ->first();

            $importe = $proyecto->importe;
            $saldo = $proyecto->saldo;
            $subtotal = $proyecto->subtotal;
            $iva_t = $proyecto->iva_t;
            $isr_r = $proyecto->isr_r;
            $iva_r = $proyecto->iva_r;
            $imp_c = $proyecto->imp_c;
            
            if($sucursal == null){
                $mensaje = $row['id']."-".$row['marca']."-".$row['sucursal']." sucursal no existente";

                if ($mensaje != '-- sucursal no existente'){
                    ImportacionError::create([
                        'importacion_id' => $importacion->id,
                        'mensaje' => $mensaje,
                        'fecha' => now(),
                    ]);
                }
            }
            else{

                $productos =DB::table('importacion_proyecto_productos')
                    ->join('productos', 'productos.id', '=', 'importacion_proyecto_productos.producto_id')
                    ->leftjoin('tipos_productos', 'tipos_productos.id', '=', 'productos.tipos_producto_id')
                    ->leftjoin('terminos_pago_clientes', 'productos.terminos_pago_cliente_id', '=', 'terminos_pago_clientes.id')
                    ->leftjoin('movimientos_pago_clientes', function (JoinClause $join) {
                        $join->on('terminos_pago_clientes.id', '=', 'movimientos_pago_clientes.terminos_pago_cliente_id')
                        ->where('movimientos_pago_clientes.secuencia', '=', 1);
                    })
                    ->select('productos.*','tipos_productos.id as tps_id','tipos_productos.nombre as tps_nombre', 
                    'terminos_pago_clientes.id as terminos','movimientos_pago_clientes.estatus_linea_cliente_id as estatus')
                    ->where('productos.es_activo','=',1)
                    ->where('importacion_proyecto_productos.importacion_proyecto_id','=',$importacion->importacion_proyecto_id)
                    ->get();

                $posicion =DB::table('proyectos')
                    ->join('fiscal_positions', 'fiscal_positions.id', '=', 'proyectos.fiscal_position_id')
                    ->select('fiscal_positions.*')
                    ->where('proyectos.id','=',$importacion->proyecto_id)
                    ->get();

                foreach ($posicion as $pos){
                    $iva_t = $pos->iva_t;
                    $isr_r = $pos->isr_r;
                    $iva_r = $pos->iva_r;
                    $imp_c = $pos->imp_c;
                }
                
                foreach ($productos as $producto){
                    $prod = str_replace(' ', '_', strtolower($producto->nombre));
                    $prod = str_replace('á', 'a', $prod);
                    $prod = str_replace('é', 'e', $prod);
                    $prod = str_replace('í', 'i', $prod);
                    $prod = str_replace('ó', 'o', $prod);
                    $prod = str_replace('ú', 'u', $prod);
                    $prod = str_replace('.', '', $prod);
                    $prod = str_replace('ñ', 'n', $prod);
                    if($row[$prod] > 0){

                        if($producto->iva <> 16){
                            $iva_t = $producto->iva;
                            $iva_r = $producto->iva;    
                        }

                        $subtotal_linea = $row[$prod];
                        $iva_t_linea = $subtotal_linea * ($iva_t / 100);
                        $isr_r_linea = $subtotal_linea * ($isr_r / 100);
                        $iva_r_linea = $subtotal_linea * ($iva_r / 100);
                        $imp_c_linea = $subtotal_linea * ($imp_c / 100);
                        $total_linea = $subtotal_linea + $iva_t_linea - $isr_r_linea - $iva_r_linea - $imp_c_linea;
                        ProyectoLinea::create([
                            'proyecto_id' => $proyecto->id,
                            'cliente_id' => $sucursal->cliente_id,
                            'sucursal_id' => $sucursal->id,
                            'producto_id' => $producto->id,
                            'cantidad' => 1,
                            'subtotal_v' => $subtotal_linea,
                            'iva_t_v' => $iva_t_linea,
                            'isr_r_v' => $isr_r_linea,
                            'iva_r_v' => $iva_r_linea,
                            'imp_c_v' => $imp_c_linea,
                            'total_v' => $total_linea,
                            'saldocliente' => $total_linea,
                            'subtotal_c' => 0,
                            'iva_t_c' => 0,
                            'isr_r_c' => 0,
                            'iva_r_c' => 0,
                            'imp_c_c' => 0,
                            'total_c' => 0,
                            'saldoproveedor' => 0,
                            'cxc' => 0,
                            'cxp' => 0,
                            'saldoproveedor' => 0,
                            'terminos_pago_cliente_id' => $producto->terminos,
                            'estatus_linea_cliente_id' => 1,
                        ]);

                        $subtotal_p += $subtotal_linea;
                        $iva_t_p += $iva_t_linea;
                        $isr_r_p += $isr_r_linea;
                        $iva_r_p += $iva_r_linea;
                        $imp_c_p += $imp_c_linea;
                        $importe += $total_linea;
                        $saldo += $$total_linea;

                        $data = [
                            'subtotal' => $subtotal_p,
                            'iva_t' => $iva_t_p,
                            'isr_r' => $isr_r_p,
                            'iva_r' => $iva_r_p,
                            'imp_c' => $imp_c_p,
                            'importe' => $importe,
                            'saldo' => $saldo,
                        ];
                        
                        $proy = DB::table('proyectos')
                            ->where('id','=',$proyecto->id)
                            ->update($data);
                    }
                };
            };    
        }
        
    }

    public function headingRow(): int
    {
        return 3;
    }
}
