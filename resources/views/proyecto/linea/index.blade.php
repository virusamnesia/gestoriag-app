@extends('adminlte::page')

@section('title', 'Proyecto')

@section('content_header')
    <h1>Partidas del Proyecto</h1>
    @if(Session::get('Error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!  </strong>{{  Session::get('Error'); }} @php if ($import > 0) echo '<a href="/proyectos/errores/'.$import.'">Ver errores</a>'; @endphp
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
    <div class="row">
        <div class="col-md-11">
        </div>
        <div class="col-md-1">
            <x-adminlte-button label="Nuevo" theme="info" icon="fas fa-info-circle" onclick="nuevo({{$proyecto->id}})"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Sucursal</th>
                    <th scope="col">Domicilio</th>
                    <th scope="col">Municipio</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Saldo</th>
                    <th scope="col">Terminos</th>
                    <th scope="col">Estatus</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($lineas as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->sucursal}}</th>
                            <td>{{$row->domicilio}}</td>
                            <td>{{$row->municipio}}</td>
                            <td>{{$row->estado}}</td>
                            <td>{{$row->producto}}</td>
                            <td>{{$row->tipo}}</td>
                            <td>${{number_format($row->precio, 2)}}</td>
                            <td>${{number_format($row->saldocliente, 2)}}</td>
                            <td>{{$row->terminos}}</td>
                            <td>{{$row->estatus}}</td>
                            <td>
                                <span class="pull-right">
                                    <div class="dropdown">
                                        <button class="btn btn-grey dropdown-toggle" type="button" id="dropdownmenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones<span class="caret"></span></button>
                                        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownmenu1">
                                            <li><button class="btn align-self-left" id="btnedit"  onclick="edit({{$proyecto->id}},{{$row->id}})"><i class="icon ion-md-create"></i>Editar</button></li>
                                            <li><button class="btn align-self-left" id="btnview" onclick="view({{$proyecto->id}},{{$row->id}})"><i class="ion-md-chatboxes"></i>Historial</button></li>
                                            <li><button class="btn align-self-left" id="btnmove" onclick="move({{$proyecto->id}},{{$row->id}})"><i class="ion-md-chatboxes"></i>Actualizar</button></li>
                                            <li><button class="btn align-self-left" id="btndelete" onclick="delete({{$row->id}})"><i class="icon ion-md-albums"></i>Cancelar</button></li>
                                    </div>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            <table>
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
            var base = "<?php echo '/proyectos/lineas/nuevo/' ?>";
            var url = base+id;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
        function edit(idp,idl){
            var base = "<?php echo '/proyectos/lineas/'?>";
            var url = base+idp+"/"+idl;
            location.href=url;
        }

        function view(idp,idl){
            var base = "<?php echo '/proyectos/lineas/sucursales/'?>";
            var url = base+idp+"/"+idl;
            location.href=url;
        }

        function move(idp,idl){
            var base = "<?php echo '/proyectos/lineas/sucursales/nuevo/'?>";
            var url = base+idp+"/"+idl;
            location.href=url;
        }
    </script>
@stop