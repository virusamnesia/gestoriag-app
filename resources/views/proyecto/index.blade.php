@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')
    <h1>Proyectos</h1>
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
            <x-adminlte-button label="Nuevo" theme="info" icon="fas fa-info-circle" onclick="nuevo()"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Proyecto</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Año</th>
                    <th scope="col">Importe</th>
                    <th scope="col">Saldo</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Cotización</th>
                    <th scope="col">Autorización</th>
                    <th scope="col">Finalización</th>
                    <th scope="col">Cancelación</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($proyectos as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->nombre}}</th>
                            <td>{{$row->cliente}}</td>
                            <td>{{$row->anio}}</td>
                            <td>${{number_format($row->importe, 2)}}</td>
                            <td>${{number_format($row->saldo, 2)}}</td>
                            <td>{{$row->estado}}</td>
                            <td>{{$row->fecha_cotizacion}}</td>
                            <td>{{$row->fecha_autorizacion}}</td>
                            <td>{{$row->fecha_finalizacion}}</td>
                            <td>{{$row->fecha_cancelacion}}</td>
                            <td>
                                <span class="pull-right">
                                    <div class="dropdown">
                                        <button class="btn btn-grey dropdown-toggle" type="button" id="dropdownmenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones<span class="caret"></span></button>
                                        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownmenu1">
                                            <li><button class="btn align-self-left" id="btnedit"  onclick="edit({{$row->id}})"><i class="icon ion-md-create"></i>Editar</button></li>
                                            <li><button class="btn align-self-left" id="btnview" onclick="view({{$row->id}})"><i class="ion-md-chatboxes"></i>Ver</button></li>
                                            <li><button class="btn align-self-left" id="btnmatriz" onclick="matriz({{$row->id}})"><i class="ion-md-chatboxes"></i>Matriz</button></li>
                                            <li><button class="btn align-self-left" id="btnmatrizcxc" onclick="matrizcxc({{$row->id}})"><i class="ion-md-chatboxes"></i>Matriz CxC</button></li>
                                            <li><button class="btn align-self-left" id="btnmatrizsaldos" onclick="matrizsaldos({{$row->id}})"><i class="ion-md-chatboxes"></i>Matriz Saldos</button></li>
                                            @if($row->autorizar == 0)
                                                <li><button class="btn align-self-left" id="btnauth" onclick="term({{$row->id}})"><i class="ion-md-chatboxes"></i>Autorizar</button></li>
                                                <li><button class="btn align-self-left" id="btnterm" onclick="term({{$row->id}})"><i class="ion-md-chatboxes"></i>Terminos de Pago</button></li>
                                            @endif
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
        function nuevo(){
            var base = "<?php echo '/proyectos/nuevo' ?>";
            var url = base;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
        function edit(id){
            var base = "<?php echo '/proyectos/lineas/'?>";
            var url = base+id;
            location.href=url;
        }

        function view(id){
            var base = "<?php echo '/proyectos/show/'?>";
            var url = base+id;
            location.href=url;
        }

        function auth(id){
            var base = "<?php echo '/proyectos/auth/'?>";
            var url = base+id;
            location.href=url;
        }

        function term(id){
            var base = "<?php echo '/proyectos/terminos/'?>";
            var url = base+id;
            location.href=url;
        }

        function matriz(id){
            var base = "<?php echo '/proyectos/matriz/'?>";
            var url = base+id;
            location.href=url;
        }

        function matrizcxc(id){
            var base = "<?php echo '/proyectos/matrizcxc/'?>";
            var url = base+id;
            location.href=url;
        }

        function matrizsaldos(id){
            var base = "<?php echo '/proyectos/matrizsaldos/'?>";
            var url = base+id;
            location.href=url;
        }
    </script>
@stop