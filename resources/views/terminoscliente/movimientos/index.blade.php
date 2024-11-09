@extends('adminlte::page')

@section('title', 'Movimientos')

@section('content_header')
    <h1>Movimientos de Pago</h1>
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
    <h4>Termino de pago: {{$termino->nombre}}</h4>

    <div class="row">
        <div class="col-md-11">
        </div>
        <div class="col-md-1">
            <x-adminlte-button label="Nuevo" theme="info" icon="fas fa-info-circle" id="btnagregar" data-toggle="modal" data-target="#smagregar" onclick="nuevo()"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-8">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Secuencia</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Estatus Pago</th>
                    <th scope="col">Facturable</th>
                    <th scope="col">Porcentaje</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($movimientos as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->secuencia}}</th>
                            <td>{{$row->nombre}}</td>
                            <td>{{$row->estatus}}</td>
                            @if ($row->facturable == False)
                            <td>No</td>
                            @else
                            <td>Sí</td>
                            @endif
                            <td>{{$row->porcentaje}}</td>
                            <td>
                                <span class="pull-right">
                                    <div class="dropdown">
                                        <button class="btn btn-grey dropdown-toggle" type="button" id="dropdownmenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones<span class="caret"></span></button>
                                        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownmenu1">
                                            <li><button class="btn align-self-left" id="btnedit"  onclick="edit({{$row->id}})"><i class="icon ion-md-create"></i>Editar</button></li>
                                            <li><button class="btn align-self-left" id="btnview" onclick="view({{$row->id}})"><i class="ion-md-chatboxes"></i>Ver</button></li>
                                            <li><button class="btn align-self-left" id="btndelete" onclick="delete({{$row->id}})"><i class="icon ion-md-albums"></i>Borrar</button></li>
                                    </div>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-2">
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <x-adminlte-button label="Regresar" type="button" theme="info" icon="far fa-hand-point-left" onclick="back()"/>
        </div>
        <div class="col-md-10">
        </div>
        <div class="col-md-1">
        </div>
    </div>

    <!-- Button trigger modal para procesar -->
        <!-- Modal -->
        <div class="modal fade" id="smagregar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="form p-3" action="/termclie/movimientos/{{$id}}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <br>
                            <h6>Nuevo Movimiento</h6>
                            <br>
                            <div class="row">
                                <x-adminlte-input name="nombre" placeholder="Nombre del movimiento" label-class="text-lightblue" fgroup-class="col-md-12">
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text">
                                            <i class="fas fa-user text-lightblue"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input>
                            </div>
                            <div class="row">
                                <x-adminlte-select2 name="estatus" label-class="text-lightblue"  fgroup-class="col-md-12"
                                    igroup-size="sm" data-placeholder="Selecciona un estatus de pago..." >
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text bg-gradient-info">
                                            <i class="far fa-building"></i>
                                        </div>
                                    </x-slot>
                                    <option/>
                                    @foreach ($estatus as $rowe)
                                    <option value="{{$rowe->id}}">{{$rowe->nombre}}</option>
                                    @endforeach
                                </x-adminlte-select2>
                            </div>
                            <div class="row">
                                <x-adminlte-select2 name="facturable" label-class="text-lightblue"  fgroup-class="col-md-12"
                                    igroup-size="sm" data-placeholder="Es facturable?..." >
                                    <x-slot name="prependSlot">
                                        <div class="input-group-text bg-gradient-info">
                                            <i class="far fa-building"></i>
                                        </div>
                                    </x-slot>
                                    <option/>
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </x-adminlte-select2>
                            </div>
                            <div class="row">
                                <x-adminlte-input name="porcentaje" placeholder="Porcentaje" type="number" fgroup-class="col-md-5"
                                    igroup-size="sm" min=1 max=1000>
                                    <x-slot name="appendSlot">
                                        <div class="input-group-text bg-light">
                                            <i class="fas fa-percent"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input>
                            </div>
                            <br>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"  id="procesar">Procesar</button>
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
            var base = "<?php echo '/termclie/' ?>";
            var url = base;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
    function delete(id){
       
    }
    </script>

    <script type="text/javascript">
        function edit(id){
            
        }
    </script>
@stop