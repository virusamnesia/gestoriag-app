@extends('adminlte::page')

@section('title', 'Precios')

@section('content_header')
    <h1>Listas de Precios</h1>
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
    @if ($id > 0)
        <h4>Cliente: {{$cliente->nombre}}</h4>
    @endif
    <div class="row">
        <div class="col-md-11">
        </div>
        <div class="col-md-1">
            <x-adminlte-button label="Nuevo" theme="info" icon="fas fa-info-circle" onclick="nuevo({{$id}})"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-8">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Clave</th>
                    <th scope="col">Nombre</th>
                    @if ($id == 0)
                        <th scope="col">Cliente</th>
                    @endif
                    <th scope="col">Inicio</th>
                    <th scope="col">Final</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($listas as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->alias}}</th>
                            <td>{{$row->nombre}}</td>
                            @if ($id == 0)
                                <td>{{$row->cliente}}</td>
                            @endif
                            <td>{{$row->inicio}}</td>
                            <td>{{$row->final}}</td>
                            <td>
                                <span class="pull-right">
                                    <div class="dropdown">
                                        <button class="btn btn-grey dropdown-toggle" type="button" id="dropdownmenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones<span class="caret"></span></button>
                                        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownmenu1">
                                            <li><button class="btn align-self-left" id="btnedit"  onclick="edit({{$id}},{{$row->id}})"><i class="icon ion-md-create"></i>Editar</button></li>
                                            <li><button class="btn align-self-left" id="btndetalle" onclick="productos({{$id}},{{$row->id}})"><i class="icon ion-md-create"></i>Productos</button></li>
                                            <li><button class="btn align-self-left" id="btnview" onclick="view({{$id}},{{$row->id}})"><i class="ion-md-chatboxes"></i>Ver</button></li>
                                            <li><button class="btn align-self-left" id="btndelete" onclick="delete({{$id}},{{$row->id}})"><i class="icon ion-md-albums"></i>Borrar</button></li>
                                    </div>
                                </span>
                            </td>
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
        function nuevo(id){
            var base = "<?php echo '/listas/nuevo/' ?>";
            var url = base+id;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
        function delete(id){
            
        }
    </script>

    <script type="text/javascript">
        function edit(idc,idl){
            var base = "<?php echo '/listas/' ?>";
            var url = base+idc+'/'+idl;
            location.href=url;
        }

        function view(idc,idl){
            var base = "<?php echo '/listas/show' ?>";
            var url = base+idc+'/'+idl;
            location.href=url;
        }
    </script>
@stop