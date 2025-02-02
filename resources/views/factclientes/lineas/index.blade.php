@extends('adminlte::page')

@section('title', 'LineasFactura')

@section('content_header')
    <h1>Líneas de la factura</h1>
    @if(Session::get('Error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error! </strong>{{  Session::get('Error'); }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if(Session::get('Exito'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Exito! </strong>{{  Session::get('Exito'); }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
@stop

@section('content')
    <h4>Cliente: {{$cliente}}</h4>
    <h4>Proyecto: {{$proyecto}}</h4>
    <h4>Subtotal Original: ${{number_format($subtotal,2)}}</h4>
    
    <form action="/factclientes/lineas/store/{{$proyecto_id}}/{{$cliente_id}}" method="POST">
            
        @csrf
        <div class="row">
            <div class="col-md-11">
                <x-adminlte-input name="subtotal" id="subtotal" placeholder="Subtotal" label="Subtotal Seleccionado" disabled
                        fgroup-class="col-md-8" disable-feedback value="{{$subtotal}}"/>
            </div>
            <div class="col-md-1">
                <x-adminlte-button class="btn-flat" type="submit" label="Confirmar" theme="info" icon="fas fa-lg fa-save"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
                <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                    <thead class="bg-dark text-white">
                    <tr>
                        <th scope="col">Facturar</th>
                        <th scope="col">Sucursal</th>
                        <th scope="col">Municipio</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Producto</th>
                        <th scope="col">Motivo</th>
                        <th scope="col">Porcentaje aplicado</th>
                        <th scope="col">Fecha aplicación</th>
                        <th scope="col">Importe</th>
                        <th scope="col">Agrupador de Facturación</th>
                        <th scope="col">Tipo de Producto</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($movimientos as $row) {{-- Add here extra stylesheets --}}
                            @php $name = "sel".$row->mov_id; @endphp
                            <tr>
                                <th scope="row">
                                    <x-adminlte-input-switch name="{{$name}}" id="{{$name}}" data-on-color="success" data-off-color="danger" checked onchange="recalcular('{{$name}}','{{$row->cxc}}')"/>
                                </th>
                                <td>{{$row->sucursal}}</td>
                                <td>{{$row->municipio}}</td>
                                <td>{{$row->estado}}</td>
                                <td>{{$row->producto}}</td>
                                <td>{{$row->estatus}}</td>
                                <td>{{$row->porcentaje}}%</td>
                                <td>{{$row->fecha}}</td>
                                <td>${{number_format($row->cxc,2)}}</td>
                                <td>{{$row->agrupador}}</td>
                                <td>{{$row->tipo}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-1">
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-1">
            <x-adminlte-button label="Regresar" type="button" theme="info" icon="far fa-hand-point-left" onclick="back()"/>
        </div>
        <div class="col-md-10">
        </div>
        <div class="col-md-1">
        </div>
    </div>

@stop

@section('plugins.BootstrapSwitch', true)

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-W8fXfP3gkOKtndU4JGtKDvXbO53Wy8SZCQHczT5FMiiqmQfUpWbYdTil/SxwZgAN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.jss"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.1/js/dataTables.bootstrap5.min.js"></script>

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#tablarow').DataTable({
                dom: 'Bfrtip',
                paging: false,
                scrollY: 400,
                select: true,
                buttons: [
                    'copy', 'csv', 'excel', 
                    {
                        extend: 'pdfHtml5',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    }, 
                    'print'
                ]
            });
        } );
    </script>

    <script type="text/javascript">
        function back(){
            var base = "<?php echo '/factclientes' ?>";
            var url = base;
            location.href=url;
        }

        function recalcular(nombre,importe){
            //var subtotal = $("#subtotal").value();
            //var elemento = "#".nombre;
            
            //if($(elemento).value()){
                //subtotal = subtotal + importe;
            //}

            //else{
                //subtotal = subtotal - importe;
            //}
            
            $("#subtotal").value(1000);
        }
    </script>

@stop