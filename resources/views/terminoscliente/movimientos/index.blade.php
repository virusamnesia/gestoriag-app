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
                    <th scope="col">Estatus Pago</th>
                    <th scope="col">Porcentaje Cliente</th>
                    <th scope="col">Porcentaje Proveedor</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($movimientos as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->secuencia}}</th>
                            <td>{{$row->estatus}}</td>
                            <td>{{$row->valor_cliente}}</td>
                            <td>{{$row->valor_proveedor}}</td>
                            <td><x-adminlte-button label="Editar" theme="warning" id="btneditar" data-toggle="modal" data-target="#smeditar" onclick="editar('{{$row->id}}','{{$row->secuencia}}','{{$row->estatus}}','{{$row->valor_cliente}}','{{$row->valor_proveedor}}')"/></td>
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
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="form p-3" action="/termclie/movimientos/nuevo/{{$id}}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <br>
                            <h6>Nuevo Movimiento</h6>
                            <br>
                            <div class="row">
                                <x-adminlte-select2 name="estatus" label-class="text-lightblue"  fgroup-class="col-md-12" label="Estatus"
                                    igroup-size="sm" data-placeholder="Selecciona un estatus de pago..." label-class="text-lightblue">
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
                                <x-adminlte-input name="vcliente" placeholder="Porcentaje Cliente" type="number" fgroup-class="col-md-5"
                                    igroup-size="sm" min=0 max=1000 label="% Cliente" label-class="text-lightblue">
                                    <x-slot name="appendSlot">
                                        <div class="input-group-text bg-light">
                                            <i class="fas fa-percent"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input>
                            </div>
                            <div class="row">
                                <x-adminlte-input name="vproveedor" placeholder="Porcentaje Proveeddor" type="number" fgroup-class="col-md-5"
                                    igroup-size="sm" min=0 max=1000  label="% Proveedor" label-class="text-lightblue">
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
                            <button type="submit" class="btn btn-primary"  id="procesar">Guardar</button>
                        </div>
                    </form> 
                </div>
            </div>
        </div>

    <!-- Button trigger modal para editar -->
        <!-- Modal -->
        <div class="modal fade" id="smeditar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Editar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="form p-3" action="/termclie/movimientos/update/{{$id}}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <br>
                            <h6>Editar Movimiento</h6>
                            <br>
                            <div class="row">
                                <x-adminlte-input name="esecuencia" id="esecuencia" placeholder="Secuencia" disabled label="Secuencia" label-class="text-lightblue"
                                    fgroup-class="col-md-8" disable-feedback/>
                            </div>
                            <div class="row">
                                <x-adminlte-input name="eestatus" id="eestatus" placeholder="Estado" disabled label="Estatus" label-class="text-lightblue"
                                    fgroup-class="col-md-8" disable-feedback/>
                            </div>
                            <div class="row">
                                <input type="hidden" name="eid" id="eid">
                                <x-adminlte-input name="evcliente" id="evcliente" placeholder="Porcentaje Cliente" type="number" fgroup-class="col-md-5"
                                    igroup-size="sm" min=0 max=1000 label="% Cliente" label-class="text-lightblue">
                                    <x-slot name="appendSlot">
                                        <div class="input-group-text bg-light">
                                            <i class="fas fa-percent"></i>
                                        </div>
                                    </x-slot>
                                </x-adminlte-input>
                            </div>
                            <div class="row">
                                <x-adminlte-input name="evproveedor" id="evproveedor" placeholder="Porcentaje Proveeddor" type="number" fgroup-class="col-md-5"
                                    igroup-size="sm" min=0 max=1000 label="% Proveedor" label-class="text-lightblue">
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
                            <button type="submit" class="btn btn-primary"  id="procesar">Guardar</button>
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
        function editar(id,sec,est,cli,pro){
            $("#eid").val(id);
            $("#esecuencia").val(sec);
            $("#eestatus").val(est).trigger('change');
            $("#evcliente").val(cli);
            $("#evproveedor").val(pro);
        }
    </script>
@stop