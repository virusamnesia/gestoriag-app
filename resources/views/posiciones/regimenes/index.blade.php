@extends('adminlte::page')

@section('title', 'Regimenes Fiscales')

@section('content_header')
    <h1>Posiciones Fiscales</h1>
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
    <form action="/posiciones/regimenes/store" method="POST">
                
        @csrf
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
                <x-adminlte-input name="id_sat" placeholder="ID SAT"
                    fgroup-class="col-md-5" disable-feedback/>
                <x-adminlte-input name="nombre" placeholder="Nombre"
                    fgroup-class="col-md-5" disable-feedback/>
            </div>
            <div class="col-md-1">
                <x-adminlte-button label="Nuevo" type="submit" theme="info" icon="fas fa-info-circle"/>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-8">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">ID SAT</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($regimenes as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->id_sat}}</th>
                            <td>{{$row->nombre}}</td>
                            <td>
                                 <button class="btn align-self-left" id="btnedit" data-toggle="modal" data-target="#smeditar" onclick="edit({{$row->id}},'{{$row->nombre}}','{{$row->id_sat}}')"><i class="icon ion-md-create"></i>Editar</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            <table>
        </div>
        <div class="col-md-2">
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            
        </div>
        <div class="col-md-10">
          </div>
        <div class="col-md-1">
            <x-adminlte-button label="Regresar" type="button" theme="info" icon="fas fa-globe-americas" onclick="back()"/>
        </div>
    </div>

    <!-- Button trigger modal para procesar -->
    <!-- Modal -->
    <div class="modal fade" id="smeditar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form p-3" action="/posiciones/regimenes/update" method="POST">
                    @csrf
                    <div class="modal-body">
                        <br>
                        <h6>Editar Regimen Fiscal</h6>
                        <br>
                        <div class="row">
                            <x-adminlte-input name="id_sat_e" label="ID SAT" placeholder="ID SAT" type="number" fgroup-class="col-md-5"
                                igroup-size="sm" min=1 max=100>
                                <x-slot name="appendSlot">
                                    <div class="input-group-text bg-light">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="nombre_e" label="Nombre" placeholder="Nombre del Regimen Fiscal" label-class="text-lightblue" 
                            fgroup-class="col-md-12" required>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-highlighter text-lightblue"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                            <input type="hidden" id="id" name="id">
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"  id="update">Actualziar</button>
                    </div>
                </form> 
            </div>
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
        function back(){
            var base = "<?php echo '/posiciones' ?>";
            var url = base;
            location.href=url;
        }

        function edit(id,nombre,id_sat){
            $("#id").val(id);
            $("#nombre_e").val(nombre);
            $("#id_sat_e").val(id_sat);
        }
    </script>

@stop