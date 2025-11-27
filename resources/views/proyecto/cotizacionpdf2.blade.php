<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 28px;
        }

        .contenedor {
            padding: 20px;
        }

        .seccion-doble {
            display: flex;
            flex-direction: row;       
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }

        .caja {
            flex: 1;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 4px rgba(0,0,0,0.2);
        }

        /* ------------------------------
        Estilos responsivos (móviles)
        ------------------------------ */
        @media (max-width: 768px) {
            .seccion-doble {
                flex-direction: column;  /* Cambia a vertical en pantallas pequeñas */
            }

            .caja {
                width: 40%;             /* Ocupan todo el ancho */
            }
        }


        h3 {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #ecf0f1;
        }

        .totals {
            width: 40%;
            float: right;
            margin-right: 10px;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
            text-align: right;
        }

        .conclusion {
            margin-top: 30px;
            padding: 15px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 0px 4px rgba(0,0,0,0.2);
        }

        footer {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <header>
        Gestoría G
    </header>

    <div class="contenedor">
        <!-- Sección doble -->
        <div class="seccion-doble">
            <div class="caja">
                <h3>Información del Cliente</h3>
                <p>Cliente:  {{$cliente->nombre}}</p>
                <p>Dirección:  {{$cliente->domicilio}}</p>
                <p>Correo:  {{$cliente->email}}</p>
            </div>

            <div class="caja">
                <h3>Datos del Documento</h3>
                <p>Fecha: {{$fecha}}</p>
                <p>Tipo de documento: {{$titulo}}</p>
            </div>
        </div>

        <!-- Introducción -->
        <p>
            Por medio del presente documento se detallan los productos solicitados,
            incluyendo precios, impuestos aplicables y subtotales correspondientes.
        </p>

        <!-- Tabla de productos -->
        <table>
            <thead>
                <tr>
                    <th scope="col">Marca</th>
                    <th scope="col">Sucursal</th>
                    <td scope="col">Producto</td>
                    <td scope="col">Cantidad</td>
                    <td scope="col">Precio</td>
                    <td scope="col">Subtotal</td>
                </tr>
            </thead>
            <tbody>
                @php $totalcliente = 0; $subtotal =0; $iva_t =0;$iva_r =0;$isr_r =0;$imp_c =0;@endphp
                @foreach ($lineas as $row) {{-- Add here extra stylesheets --}}
                    <tr>
                        <th scope="row">{{$row->marca}}</th>
                        <th scope="row">{{$row->sucursal}}</th>
                        <td>{{$row->producto}}</td>
                        <td>{{$row->cantidad}}</td>
                        <td>${{number_format($row->precio,2)}}</td>
                        <td>${{number_format($row->subtotal_v,2)}}</td>
                         @php 
                            $subtotal += $row->subtotal_v; 
                            $iva_t += $row->iva_t_v; 
                            $isr_r += $row->isr_r_v;
                            $iva_r += $row->iva_r_v; 
                            $imp_c += $row->imp_c_v; 
                            $totalcliente += $row->total_v; 
                        @endphp
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Totales -->
        <div>
            <table class="totals">
                <tbody>
                    <tr>
                        <th scope="row">Subtotal</th>
                        <td>${{number_format($proyecto->subtotal,2)}}</td>
                    </tr>
                    <tr>
                        <th scope="row">IVA Trasladado</th>
                        <td>${{number_format($proyecto->iva_t,2)}}</td>
                    </tr>
                    <tr>
                        <th scope="row">ISR Retenido</th>
                        <td>${{number_format($proyecto->isr_r,2)}}</td>
                    </tr>
                    <tr>
                        <th scope="row">IVA Retenido</th>
                        <td>${{number_format($proyecto->iva_r,2)}}</td>
                    </tr>
                    <tr>
                        <th scope="row">Impuesto Cedular</th>
                        <td>${{number_format($proyecto->imp_c,2)}}</td>
                    </tr>
                    <tr>
                        <th scope="row">Total</th>
                        <td>${{number_format($proyecto->importe,2)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <!-- Conclusión -->
        <div class="conclusion">
            <h3>Condiciones Comerciales</h3>
            <p>
                Las condiciones comerciales aplicables a este documento incluyen formas 
                de pago, tiempos de entrega y políticas de garantía conforme a los 
                acuerdos previamente establecidos.
            </p>
        </div>
    </div>

    <footer>
        © 2025 - Nombre Comercial de la Empresa — Fecha de emisión
    </footer>

</body>
</html>
