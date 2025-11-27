<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{$titulo}}</title>
    <style>
        /* Estilos Generales */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        /* Contenedor Principal (Simula una hoja de papel) */
        .document-container {
            background-color: #ffffff;
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px;
            /*box-shadow: 0 0 10px rgba(0,0,0,0.1);*/
            border-radius: 5px;
        }

        /* 1. Encabezado */
        header {
            border-bottom: 2px solid #0056b3;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #0056b3;
            text-transform: uppercase;
        }

        /* 2. Sección Dividida (Cliente y Datos) */
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }

        .client-info, .doc-data {
            width: 48%;
        }

        h3 {
            font-size: 16px;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        p {
            margin: 5px 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .doc-data p strong {
            display: inline-block;
            width: 120px;
        }

        /* 3. Párrafo de Introducción */
        .intro-text {
            margin-bottom: 30px;
            font-style: italic;
            color: #666;
        }

        /* 4. Tabla de Productos */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 14px;
        }

        table th {
            background-color: #0056b3;
            color: #ffffff;
            padding: 12px;
            text-align: left;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        /* Alineación de números a la derecha */
        .text-right {
            text-align: right;
        }

        /* Estilos para totales */
        .totals-row td {
            font-weight: bold;
            border-bottom: none;
        }

        .grand-total {
            font-size: 18px;
            color: #0056b3;
            border-top: 2px solid #0056b3;
        }

        /* 5. Conclusión y Condiciones */
        .terms-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            font-size: 12px;
            margin-bottom: 40px;
            border-left: 4px solid #ddd;
        }

        /* 6. Pie de Página */
        footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="document-container">
        
        <header>
            <div class="company-name">Gestoría G S.A. de C.V.</div>
            </header>

        <div class="info-section">
            <div class="client-info">
                <h3>Datos del Cliente</h3>
                <p><strong>Razón Social:</strong> {{$cliente->nombre}}</p>
                <p><strong>RFC:</strong> {{$cliente->rfc}}</p>
                <p><strong>Dirección:</strong> Av. Industrial 450, Monterrey, NL.</p>
                <p><strong>Contacto:</strong> Lic. Juan Pérez</p>
            </div>
            
            <div class="doc-data">
                <h3>Detalles del Documento</h3>
                <p><strong>Tipo:</strong> {{$titulo}}</p>
                <p><strong>Folio:</strong> {{$proyecto->nombre}}</p>
                <p><strong>Fecha Emisión:</strong> {{$fecha}}</p>
                <p><strong>Moneda:</strong> MXN - Peso Mexicano</p>
            </div>
        </div>

        <div class="intro-text">
            <p>Estimado cliente, a continuación presentamos el desglose de los productos y servicios solicitados bajo la orden de compra OC-2025, agradeciendo de antemano su preferencia.</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th scope="col">Marca</th>
                    <th scope="col">Sucursal</th>
                    <th scope="col">Domicilio</th>
                    <th scope="col">Municipio</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Superficie</th>
                    @php $totalproducto = [];@endphp
                    @foreach ($productos as $prod)
                    <th>{{$prod->producto}}</th>
                    @php $totalproducto[$prod->producto] = 0;@endphp
                    @endforeach
                    <th scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalcliente = 0; $sucact =0;@endphp
                @foreach ($lineas as $row) {{-- Add here extra stylesheets --}}
                    @if ($sucact != $row->sucursal_id)
                    @php $totalcliente = 0; $sucact = $row->sucursal_id; @endphp
                    <tr>
                        <th scope="row">{{$row->marca}}</th>
                        <th scope="row">{{$row->sucursal}}</th>
                        <td>{{$row->domicilio}}</td>
                        <td>{{$row->municipio}}</td>
                        <td>{{$row->estado}}</td>
                        <td>{{$row->superficie}}</td>
                        @foreach ($productos as $prod)
                            @php $valprod =0; @endphp
                            @foreach ($lineas as $lin)
                                @if ($lin->sucursal_id == $row->sucursal_id and $lin->producto == $prod->producto)
                                    <td>${{number_format($lin->total_v, 2)}}</td>
                                    @php 
                                        $totalcliente += $lin->total_v; 
                                        $valprod = 1;
                                        $totalproducto[$prod->producto] += $lin->total_v;
                                    @endphp
                                @endif
                            @endforeach
                            @if($valprod == 0)
                                <td>${{number_format(0, 2)}}</td>
                            @endif
                        @endforeach
                        <td>${{number_format($totalcliente, 2)}}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @php $total = 0;@endphp
                    @foreach ($productos as $prod)
                    <th>${{number_format($totalproducto[$prod->producto], 2)}}</th>
                    @php $total += $totalproducto[$prod->producto];@endphp
                    @endforeach
                    <td>${{number_format($total, 2)}}</td>
                </tr>
            </tfoot>
        </table>

        <div class="terms-section">
            <strong>Condiciones Comerciales:</strong>
            <p>El pago deberá realizarse dentro de los 30 días naturales posteriores a la fecha de emisión. Los precios están sujetos a cambios sin previo aviso en nuevas cotizaciones. En caso de retraso en el pago, se aplicará un interés moratorio del 3% mensual.</p>
        </div>

        <footer>
            <p>Documento generado el {{$fecha}} | Soluciones Globales S.A. de C.V.</p>
            <p>Este documento es una representación impresa de un CFDI.</p>
        </footer>

    </div>
   

    
</body>
</html>