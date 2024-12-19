@extends('adminlte::page')

@section('title', 'Historial')

@section('content_header')
    <h1>Historial de la sucursal</h1>
    @if(Session::get('Error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!  </strong>{{  Session::get('Error'); }}
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
    <h5>Proyecto: {{$proyecto->nombre}}</h5>
    <h5>Cliente: {{$cliente->nombre}}</h5>
    <h5>Sucursal: {{$linea->sucursal}}</h5>
    <h5>Producto: {{$linea->producto}}</h5>
    <div class="row">
        <div class="col-md-11">
        </div>
        <div class="col-md-1">
            <x-adminlte-button label="Nuevo" theme="info" icon="fas fa-info-circle" onclick="nuevo({{$idp}},{{$idl}})"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Movimiento</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Observaciones</th>
                    <th scope="col">URL's</th>
                    <th scope="col">Facturable</th>
                    <th scope="col">Fecha Factura</th>
                    <th scope="col">Factura</th>
                    <th scope="col">Importe</th>
                    <th scope="col">Saldo</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($movimientos as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->movimiento}}</th>
                            <td>{{$row->fecha_mov}}</td>
                            <td>{{$row->observaciones}}</td>
                            <td><a href="{{$row->url}}">{{$row->url}}</a></td>
                            <td>{{$row->es_facturable}}</td>
                            <td>{{$row->fecha_factura}}</td>
                            <td>{{$row->factura}}</td>
                            <td>{{$row->importe}}</td>
                            <td>{{$row->saldo}}</td>
                        </tr>
                    @endforeach
                </tbody>
            <table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            
        </div>
        <div class="col-md-10">
        </div>
        <div class="col-md-1">
            <x-adminlte-button class="btn-sm" type="button" label="Cancelar" theme="outline-danger" icon="fas fa-lg fa-trash" onclick="back({{$proyecto->id}})"/>
        </div>
    </div>

@stop

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
    function back(id){
        var base = "<?php echo '/proyectos/lineas/' ?>";
        var url = base+id;
        location.href=url;
    }

    function nuevo(idp,idl){
            var base = "<?php echo '/proyectos/lineas/sucursales/nuevo/'?>";
            var url = base+idp+"/"+idl;
            location.href=url;
        }
</script>
@stop