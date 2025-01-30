@extends('adminlte::page')

@section('title', 'Facturas')

@section('content_header')
    <h1>Proyectos facturables</h1>
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
    <div class="row">
        <div class="col-md-11">
        </div>
        <div class="col-md-1">
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-8">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">RFC</th>
                    <th scope="col">Proyecto</th>
                    <th scope="col">Año</th>
                    <th scope="col">Importe Facturable</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($proyectos as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->id}}</th>
                            <td>{{$row->cliente}}</td>
                            <td>{{$row->rfc}}</td>
                            <td>{{$row->nombre}}</td>
                            <td>{{$row->anio}}</td>
                            <td>${{number_format($row->cxc, 2)}}</td>
                            <td><a href= "/factclientes/lineas/{{$row->id}}">Revisar</a></td>
                        </tr>
                    @endforeach
                </tbody>
            <table>
        </div>
        <div class="col-md-2">
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
        function nuevo(){
            var base = "<?php echo '/factclientes/nuevo' ?>";
            var url = base;
            location.href=url;
        }
        
        function detalle(id){
            var base = "<?php echo '/factclientes/lineas/' ?>";
            var url = base+id;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
        function edit(id){
            var base = "<?php echo '/factclientes/'?>";
            var url = base+id;
            location.href=url;
        }

        function view(id){
            var base = "<?php echo '/fectclientes/show/'?>";
            var url = base+id;
            location.href=url;
        }
    </script>
@stop