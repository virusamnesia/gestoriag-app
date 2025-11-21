@extends('adminlte::page')

@section('title', 'Posiciones Fiscales')

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
    
    <div class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-10">
        </div>
            <div class="col-md-1">
                <x-adminlte-button label="Nuevo" type="button" theme="info" data-toggle="modal" data-target="#smagregar" icon="fas fa-info-circle" />
        </div>
        </div>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-8">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Regimen Fiscal</th>
                    <th scope="col">IVA Trasladado</th>
                    <th scope="col">ISR Retenido</th>
                    <th scope="col">IVA Retenido</th>
                    <th scope="col">Impuesto Cedular</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($posiciones as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->nombre}}</th>
                            <td>{{$row->regimen}}</td>
                            <td>{{$row->iva_t}}</td>
                            <td>{{$row->isr_r}}</td>
                            <td>{{$row->iva_r}}</td>
                            <td>{{$row->imp_c}}</td>
                            <td>
                                 <x-adminlte-button label="Editar" theme="warning" id="btnedit" data-toggle="modal" data-target="#smeditar" onclick="edit('{{$row->id}}','{{$row->nombre}}','{{$row->regimen_id}}','{{$row->regimen}}','{{$row->iva_t}}','{{$row->isr_r}}','{{$row->iva_r}}','{{$row->imp_c}}')"/>
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
            <x-adminlte-button label="Regimenes Fiscales" type="button" theme="info" icon="fas fa-globe-americas" onclick="regimenes()"/>
        </div>
    </div>

    <!-- Button trigger modal para procesar -->
    <!-- Modal -->
    <div class="modal fade" id="smagregar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Nueva</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form p-3" action="/posiciones/store" method="POST">
                    @csrf
                    <div class="modal-body">
                        <br>
                        <h6>Nueva Posición Fiscal</h6>
                        <br>
                        <div class="row">
                            <x-adminlte-input name="nombre" placeholder="Nombre de la Posición Fiscal" label-class="text-lightblue" 
                            fgroup-class="col-md-12" required>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-highlighter text-lightblue"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-select2 name="regimen" label-class="text-lightblue"  fgroup-class="col-md-12"
                                igroup-size="sm" data-placeholder="Selecciona un Regimen Fiscal..." >
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-gradient-info">
                                        <i class="fas fa-location-dot"></i>
                                    </div>
                                </x-slot>
                                <option/>
                                @foreach ($regimenes as $rowe)
                                <option value="{{$rowe->id}}">{{$rowe->id_sat}}, {{$rowe->nombre}}</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="iva_t" placeholder="IVA Trasladado" type="number" fgroup-class="col-md-5" value="0"
                                igroup-size="sm" min=0 max=100>
                                <x-slot name="appendSlot">
                                    <div class="input-group-text bg-light">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="isr_r" placeholder="ISR Retenido" type="number" fgroup-class="col-md-5" value="0"
                                igroup-size="sm" min=0 max=100>
                                <x-slot name="appendSlot">
                                    <div class="input-group-text bg-light">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="iva_r" placeholder="IVA Retenido" type="number" fgroup-class="col-md-5" value="0"
                                igroup-size="sm" min=0 max=100>
                                <x-slot name="appendSlot">
                                    <div class="input-group-text bg-light">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="imp_c" placeholder="Impuesto Cedular" type="number" fgroup-class="col-md-5" value="0"
                                igroup-size="sm" min=0 max=100>
                                <x-slot name="appendSlot">
                                    <div class="input-group-text bg-light">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"  id="guardar">Guardar</button>
                    </div>
                </form> 
            </div>
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
                <form class="form p-3" action="/posiciones/update" method="POST">
                    @csrf
                    <div class="modal-body">
                        <br>
                        <h6>Editar Posición Fiscal</h6>
                        <br>
                        <div class="row">
                            <x-adminlte-input name="nombre_e" id="nombre_e" placeholder="Nombre de la Posición Fiscal" label-class="text-lightblue" 
                            fgroup-class="col-md-12" required disabled>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-highlighter text-lightblue"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                            <input type="hidden" id="id" name="id">
                        </div>
                        <div class="row">
                            <x-adminlte-select2 name="regimen_e" id="regimen_e" label-class="text-lightblue"  fgroup-class="col-md-12"
                                igroup-size="sm" disabled data-placeholder="Selecciona un Regimen Fiscal..." >
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-gradient-info">
                                        <i class="fas fa-location-dot"></i>
                                    </div>
                                </x-slot>
                                <option/>
                                @foreach ($regimenes as $rowe)
                                <option value="{{$rowe->id}}">{{$rowe->id_sat}}, {{$rowe->nombre}}</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="iva_t_e" id="iva_t_e" placeholder="IVA Trasladado" type="number" fgroup-class="col-md-5"
                                igroup-size="sm" min=0 max=100>
                                <x-slot name="appendSlot">
                                    <div class="input-group-text bg-light">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="isr_r_e" placeholder="ISR Retenido" type="number" fgroup-class="col-md-5"
                                igroup-size="sm" min=0 max=100>
                                <x-slot name="appendSlot">
                                    <div class="input-group-text bg-light">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="iva_r_e" placeholder="IVA Retenido" type="number" fgroup-class="col-md-5"
                                igroup-size="sm" min=0 max=100>
                                <x-slot name="appendSlot">
                                    <div class="input-group-text bg-light">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="imp_c_e" placeholder="Impuesto Cedular" type="number" fgroup-class="col-md-5"
                                igroup-size="sm" min=0 max=100>
                                <x-slot name="appendSlot">
                                    <div class="input-group-text bg-light">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
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
        function regimenes(){
            var base = "<?php echo '/posiciones/regimenes' ?>";
            var url = base;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
        function edit(id,nombre,regimen_id,regimen,iva_t,isr_r,iva_r,imp_c){
            $("#nombre_e").val(nombre);
            $("#regimen_e").val(regimen_id).trigger('change');
            $("#iva_t_e").val(iva_t);
            $("#isr_r_e").val(isr_r);
            $("#iva_r_e").val(iva_r);
            $("#imp_c_e").val(imp_c);
            $("#id").val(id);
        }
    </script>
@stop