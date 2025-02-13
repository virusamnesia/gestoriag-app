@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <h1>Roles</h1>
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
            @can('roles')
            <x-adminlte-button label="Nuevo" data-toggle="modal" data-target="#modalAdd" class="bg-teal"/>
            @endcan
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-8">
                    <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                        <thead class="bg-dark text-white">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $row) {{-- Add here extra stylesheets --}}
                                <tr>
                                    <th scope="row">{{$row->id}}</th>
                                    <td>{{$row->name}}</td>
                                    <td>
                                        <span class="pull-right">
                                            <div class="dropdown">
                                                <button class="btn btn-grey dropdown-toggle" type="button" id="dropdownmenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones<span class="caret"></span></button>
                                                <ul class="dropdown-menu pull-right" aria-labelledby="dropdownmenu1">
                                                    <li><button class="btn align-self-left" id="btnedit" data-toggle="modal" data-target="#modalEdit"  onclick="edit('{{$row->id}}','{{$row->name}}')"><i class="icon ion-md-create"></i>Editar</button></li>
                                                    <li><button class="btn align-self-left" id="btnpermisos"  onclick="permisos({{$row->id}})"><i class="icon ion-md-create"></i>Asignar Permisos</button></li>
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
        </div>
    </div>

    <x-adminlte-modal id="modalAdd" title="Nuevo Rol" size="md" theme="teal"
        icon="fas fa-bell" v-centered static-backdrop>
        <form action="/roles/store" method="POST">
             @csrf
            <div class="card">
                <div class="card-body">
                    <x-adminlte-input name="nombre" placeholder="Rol" label-class="text-lightblue" fgroup-class="col-md-12">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-user text-lightblue"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>
        <x-slot name="footerSlot">
            <x-adminlte-button type="submit" class="mr-auto" theme="success" label="Guardar"/>
        </form>
            <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal" label="Cerrar"/>
        </x-slot>
    </x-adminlte-modal>

    <x-adminlte-modal id="modalEdit" title="Editar Rol" size="md" theme="teal"
        icon="fas fa-bell" v-centered static-backdrop>
        <form action="/roles/update" method="POST">
             @csrf
            <div class="card">
                <div class="card-body">
                    <input type="hidden" name="id" id="id">
                    <x-adminlte-input name="enombre" id="enombre" placeholder="Permiso" label-class="text-lightblue" fgroup-class="col-md-12">
                        <x-slot name="prependSlot">
                            <div class="input-group-text">
                                <i class="fas fa-user text-lightblue"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
            </div>
        <x-slot name="footerSlot">
            <x-adminlte-button type="submit" class="mr-auto" theme="success" label="Guardar"/>
        </form>
            <x-adminlte-button theme="danger" label="Dismiss" data-dismiss="modal" label="Cerrar"/>
        </x-slot>
    </x-adminlte-modal>

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

    @if (session("message"))
        <script>
            $(document).ready(function() {
                let mensaje = "{{ session ('message')}}";
                Swal.fire({
                    'title': 'Resultado',
                    'text': mensaje,
                    'icon': 'success'
                })
            } );
        </script>
    @endif

    @if (session("error"))
        <script>
            $(document).ready(function() {
                let mensaje = "{{ session ('error')}}";
                Swal.fire({
                    'title': 'Resultado',
                    'text': mensaje,
                    'icon': 'error'
                })
            } );
        </script>
    @endif


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
            var base = "<?php echo '/usuarios/roles/nuevo' ?>";
            var url = base;
            location.href=url;
        }
        
    </script>

    <script type="text/javascript">
        function permisos(id){
            var base = "<?php echo '/roles/permisos/'?>";
            var url = base+id;
            location.href=url;
        }

        function edit(id,name){
            $("#id").val(id);
            $("#enombre").val(name);
        }

    </script>
@stop