<html>
<head>
  <style>
    body{
      font-family: sans-serif;
      font-size: 11px;
    }
    @page {
      margin: 160px 50px;
    }
    header { position: fixed;
      left: 0px;
      top: -120px;
      right: 0px;
      height: 50px;
      color: #949494bb;
      text-align: center;
    }
    header h1{
      margin: 10px 0;
    }
    header h2{
      margin: 0 0 10px 0;
    }

    header h4{
      margin: 10px 0;
    }
    header h5{
      margin: 0 0 10px 0;
    }
    .tablebox {
      width: 100%;
    }
    .tdbox  {
      width: 100%;
      border: 0px #ffffff;
    }
    .tdheader  {
      border: 0px #ffffff;
    }
    .trbox  {
      width: 100%;
      border: 0px #ffffff;
    }

    .contenedor {
        padding: 5px;
    }
    .h3box{
        color: #ffffff;
    }
    .h32box{
        color: #005e80fc;
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
        width: 95%;
        height: 15%;
        background-color: #005e80fc;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0px 0px 4px rgba(0,0,0,0.2);
        font-size: 11px;
    }

    .caja2 {
        flex: 1;
        width: 95%;
        height: 15%;
        color: #005e80fc;
        background-color: #ffffff;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0px 0px 4px rgba(0,0,0,0.2);
        font-size: 11px;
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
        font-size: 8px;
    }

    table th {
        background-color: #005e80fc;
        color: #ffffff;
        font-size: 10px;
    }

    .totals {
        width: 20%;
        float: right;
        margin-right: 0px;
        border-collapse: collapse;
        margin-top: 5px;
        background-color: white;
        text-align: left;
    }
    .totalsrow{
        height: 5px;
        font-size: 8px;
    }

    .simple {
        border-collapse: collapse;
        margin-top: 15px;
        border: 0px;
    }

    .conclusion {
        margin-top: 30px;
        padding: 15px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0px 0px 4px rgba(0,0,0,0.2);
    }

    footer {
      position: fixed;
      left: 0px;
      bottom: -50px;
      right: 0px;
      height: 40px;
      border-bottom: 2px solid #ddd;
      color: #bfc2c5;
    }
    footer .page:after {
      content: counter(page);
    }
    footer table {
      width: 100%;
    }
    footer table td  {
      border: 1px solid #ffffff;
    }
    footer p {
      text-align: center;
    }
    footer .izq {
      text-align: left;
    }
    .firmas {
        width: 100%;
        float: right;
        margin-right: 0px;
        border-collapse: collapse;
        margin-top: 5px;
        background-color: white;
        text-align: left;
        border: 0px #ffffff;
    }
    .tdfirmas{
      border: 0px #ffffff;
    }
    .pfirmas{
      text-align: center;
    }
    .hrfirmas{
      width: 60%;
    }
    .pjustify {
      text-align: justify;
    }
  </style>
<body>
  <header>
    <table>
        <tr>
            <td class="tdheader">
                <img src="vendor/adminlte/dist/img/Gestoria_G_corto.jpg" alt="Gestoria_G" width="100px">
            </td>
            <td class="tdheader">
                <h1>GESTORÍA G</h1>
                <h2>CONSULTORÍA Y SERVICIOS EMPRESARIALES SA DE CV</h2>
            </td>
        </tr>
    </table>
  </header>
  <footer>
    <table class="simple">
      <tr>
        <td>
            <img src="vendor/adminlte/dist/img/marcagto.jpg" alt="Marca GTO" width="50px">
        </td>
        <td>
            <p class="izq">
              <div>
                Loma del potero 215-3, Lomas del Campestre, León, Guanajuato
              </div>
              <div>
                Cel: 477 5768682      
              </div>
            </p>
        </td>
        <td>
            <img src="vendor/adminlte/dist/img/gestoria_g.png" alt="Gestoria_G" width="150px">
        </td>
        <td>
          <p class="page">
            Página
          </p>
        </td>
      </tr>
    </table>
  </footer>

  <div class="contenedor">
        <!-- Sección doble -->
        <div class="seccion-doble">
            <table class="tablebox">
                <tr class="trbox">
                    <td class="tdbox">
                        <div class="caja">
                            <h3 class="h3box">Información del Proveedor</h3>
                            <p class="h3box">Proveedor:  {{$proveedor->nombre}}</p>
                            <p class="h3box">Dirección:  {{$proveedor->domicilio}}</p>
                            <p class="h3box">RFC:  {{$proveedor->rfc}}</p>
                        </div>
                    </td>
                    <td class="tdbox">
                        <div class="caja2">
                            <h3 class="h32box">Datos del Documento</h3>
                            <p class="h32box">Fecha: {{$fecha}}</p>
                            <p class="h32box">Tipo de documento: {{$titulo}}</p>
                            <p class="h32box">Estado de documento: {{$status}}</p>
                            <p class="h32box">Identicador: {{$presupuesto->nombre}}</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Introducción -->
        
        <h4>PRESENTE</h4>
        <p class="pjustify">
            Por medio del presente, dejo a su consideración la orden de compra, 
            para diversos servicios y/o productos en las sucursales que a continuación se detallan:
        </p>
        <!-- Tabla de productos -->
        <table>
            <thead>
                <tr>
                    <th scope="col">Marca</th>
                    <th scope="col">Sucursal</th>
                    <th scope="col">Ubicación</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $totalproveedor = 0; $subtotal =0; $iva_t =0;$iva_r =0;$isr_r =0;$imp_c =0;@endphp
                @foreach ($lineas as $row) {{-- Add here extra stylesheets --}}
                    <tr>
                        <td>{{$row->marca}}</td>
                        <td>{{$row->sucursal}}</td>
                        <td">{{$row->municipio}},{{$row->estado}}</td>
                        <td>{{$row->producto}}</td>
                        <td>{{$row->cantidad}}</td>
                        <td>${{number_format($row->costo,2,".",",")}}</td>
                        <td>${{number_format($row->subtotal_c,2,".",",")}}</td>
                         @php 
                            $subtotal += $row->subtotal_c; 
                            $iva_t += $row->iva_t_c; 
                            $isr_r += $row->isr_r_c;
                            $iva_r += $row->iva_r_c; 
                            $imp_c += $row->imp_c_c; 
                            $totalproveedor += $row->total_c; 
                        @endphp
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Totales -->
        <div>
            <table class="totals">
                <tbody>
                    <tr class="totalsrow">
                        <th class="totalsrow">Subtotal</th>
                        <td class="totalsrow">${{number_format($presupuesto->subtotal,2,".",",")}}</td>
                    </tr>
                    <tr class="totalsrow">
                        <th class="totalsrow" scope="row">IVA</th>
                        <td class="totalsrow">${{number_format($presupuesto->iva_t,2,".",",")}}</td>
                    </tr>
                    <tr class="totalsrow">
                        <th class="totalsrow" scope="row">Ret ISR</th>
                        <td class="totalsrow">${{number_format($presupuesto->isr_r,2,".",",")}}</td>
                    </tr>
                    <tr class="totalsrow">
                        <th class="totalsrow" scope="row">Ret IVA</th>
                        <td class="totalsrow">${{number_format($presupuesto->iva_r,2,".",",")}}</td>
                    </tr>
                    <tr class="totalsrow">
                        <th class="totalsrow" scope="row">Ret Cedular</th>
                        <td class="totalsrow">${{number_format($presupuesto->imp_c,2,".",",")}}</td>
                    </tr>
                    <tr class="totalsrow">
                        <th class="totalsrow" scope="row">TOTAL</th>
                        <td class="totalsrow">${{number_format($presupuesto->importe,2,".",",")}}</td>
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
        <!-- Entrega -->
        <div class="conclusion">
            <h3>Condiciones de entrega</h3>
            <ol>
                <li class="pjustify">
                     Entregar los permisos concluidos y/o dictámenes en cada establecimiento de acuerdo al protocolo de entrega, 
                     que consta de: acuse con firma y sello de la sucursal, fotografía del permiso o licencia en acrílico de tienda; 
                     para PIPC se deberá entregar una copia de la portada para que también se acuse con firma y sello de la sucursal y 
                     foto de entrega (de lo anterior, lo que aplique de acuerdo al servicio cotizado). 
                     <b>Es de suma importancia que al momento de presentarse en cada establecimiento hacerlo bajo el nombre de Gestoría G.</b> 
                </li>
                <li class="pjustify">
                     Compartir a Gestoría G la evidencia de manera digital (Programa Interno, constancias de capacitación, licencias escaneadas, 
                     acuses de entrega, fotografías, etc.) y evidencia original (DC-3 del personal capacitado).
                </li>
            </ol>
        </div>

        <!-- Conclusión -->
        <div class="conclusion">
            <h3>Condiciones de pago</h3>
            <ol>
                <li class="pjustify">
                     A la autorización de la presente orden de compra, se tramitará el 50% de anticipo sobre el total una vez firmada por ambas partes. 
                </li>
                <li class="pjustify">
                     Al concluir los tramites, el 50% restante se cubrirá una vez cumpliendo las condiciones de entrega. 
                     El equipo de Gestoría G deberá revisar  dichas evidencias para ser aceptadas, en caso de haber observaciones en determinado servicio, 
                     serán indicadas y tendrán que ser atendidas con la persona correspondiente, en caso contrario se podrá tramitar el monto restante 
                     para pago sin inconveniente. 
                </li>
            </ol>
        </div>
        <br><br><br><br><br><br><br>
        <table class="firmas">
          <body>
            <tr>
              <td class="tdfirmas">
                <hr class="hrfirmas">
              </td >
              <td class="tdfirmas">
                <hr class="hrfirmas">
              </td>
            </tr>
            <tr>
              <td class="tdfirmas">
                <p class="pfirmas">{{$proveedor->nombre}}</p>
                <p class="pfirmas">Proveedor</p>
              </td>
              <td class="tdfirmas">
                <p class="pfirmas">Juan Manuel Alejandro Navarro Guzmán</p>
                <p class="pfirmas">Director Operaciones</p>
              </td>
            </tr>
          </body>
        </table>
    </div>
  </div>
</body>
</html>