@extends('adminlte::page')

@section('title', 'Partidas')

@section('content_header')
    <h1>Productos del presupuesto</h1>
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
    <h4>Presupuesto: {{$presupuesto->nombre}}</h4>
    <h4>Proveedor: {{$proveedor->nombre}}</h4>

    <div class="row">
        <div class="col-md-11">
        </div>
        <div class="col-md-1">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Proyecto</th>
                    <th scope="col">Marca</th>
                    <th scope="col">Sucursal</th>
                    <th scope="col">Domicilio</th>
                    <th scope="col">Municipio</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Costo</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">IVA Trasladado</th>
                    <th scope="col">ISR Retenido</th>
                    <th scope="col">IVA Retenido</th>
                    <th scope="col">Impuesto Cedular</th>
                    <th scope="col">Total</th>
                    <th scope="col">Saldo</th>
                    <th scope="col">CxP</th>
                    <th scope="col">Pagado</th>
                    <th scope="col">Estatus</th>
                    <th scope="col">Operación</th>
                    <th scope="col">Acciones</th
                </tr>
                </thead>
                <tbody>
                    @foreach ($lineas as $row) {{-- Add here extra stylesheets --}}
                    <tr>
                        <th scope="row">{{$row->proyecto}}</th>
                        <th scope="row">{{$row->marca}}</th>
                        <th scope="row">{{$row->sucursal}}</th>
                        <td>{{$row->domicilio}}</td>
                        <td>{{$row->municipio}}</td>
                        <td>{{$row->estado}}</td>
                        <td>{{$row->producto}}</td>
                        <td>{{$row->tipo}}</td>
                        <td>{{$row->cantidad}}</td>
                        <td>${{number_format($row->costo, 2)}}</td>
                        <td>${{number_format($row->subtotal_c, 2)}}</td>
                        <td>${{number_format($row->iva_t_c, 2)}}</td>
                        <td>${{number_format($row->isr_r_c, 2)}}</td>
                        <td>${{number_format($row->iva_r_c, 2)}}</td>
                        <td>${{number_format($row->imp_c_c, 2)}}</td>
                        <td>${{number_format($row->total_c, 2)}}</td>
                        <td>${{number_format($row->saldoproveedor, 2)}}</td>
                            <td>${{number_format($row->cxp, 2)}}</td>
                            <td>${{number_format($row->total_c-$row->saldoproveedor-$row->cxp, 2)}}</td>
                        <td>{{$row->estatus}}</td>
                        <td>{{$row->estatuslinea}}</td>
                        <td>
                            <span class="pull-right">
                                <div class="dropdown">
                                    <button class="btn btn-grey dropdown-toggle" type="button" id="dropdownmenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones<span class="caret"></span></button>
                                    <ul class="dropdown-menu pull-right" aria-labelledby="dropdownmenu1">
                                        @if($presupuesto->autorizar == 0)
                                        <li><button class="btn align-self-left" id="btnedit" data-toggle="modal" data-target="#smeditar"  onclick="editar('{{$row->id}}','{{$row->sucursal}}','{{$row->producto}}','{{$row->costo}}','{{$row->cantidad}}')"><i class="icon ion-md-create"></i>Editar</button></li>
                                        @endif
                                        @if($presupuesto->autorizar== 1)
                                        <li><button class="btn align-self-left" id="btnview" onclick="view({{$presupuesto->id}},{{$row->id}})"><i class="ion-md-chatboxes"></i>Historial</button></li>
                                            @if($row->estatuslinea_id == 1)
                                            <li><button class="btn align-self-left" id="btnmove" onclick="move({{$presupuesto->id}},{{$row->id}})"><i class="ion-md-chatboxes"></i>Actualizar</button></li>
                                            @endif
                                        @endif
                                        @if($row->estatuslinea_id == 1)
                                        <li><button class="btn align-self-left" id="btnclose"  onclick="breakup({{$presupuesto->id}},{{$row->id}})"><i class="icon ion-md-pint"></i>Interrumpir</button></li>
                                        @endif
                                        @if($presupuesto->autorizar == 0)
                                        <li><button class="btn align-self-left" id="btnborrar" onclick="borrar({{$presupuesto->id}},{{$row->id}})"><i class="icon ion-md-albums"></i>Cancelar</button></li>
                                        @endif
                                </div>
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
    <div class="modal fade" id="smeditar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form p-3" action="/presupuestos/lineas/costo/{{$presupuesto->id}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <br>
                        <h6>Editar Costo</h6>
                        <br>
                        <div class="row">
                            <input type="hidden" name="id" id="id" />
                            <x-adminlte-input name="sucursal" id="sucursal" placeholder="Nombre de la sucursal" label-class="text-lightblue" 
                            fgroup-class="col-md-12">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-user text-lightblue"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="producto" id="producto" placeholder="Nombre del producto" label-class="text-lightblue" 
                            fgroup-class="col-md-12">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-user text-lightblue"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <input type="hidden" name="cant" id="cant" />
                        <div class="row">
                            <x-adminlte-input name="cantidad" id="cantidad" placeholder="Cantidad" label-class="text-lightblue" disabled
                            fgroup-class="col-md-12">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-coins text-lightblue"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="costo" id="costo" placeholder="Costo" type="number" fgroup-class="col-md-5"  value="0"
                            igroup-size="sm" min=1 max=100000 step="0.01">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-dollar-sign text-lightblue"></i>
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
                select: true,
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
            var base = "<?php echo '/presupuestos' ?>";
            var url = base;
            location.href=url;
        }

        function move(idp,idl){
            var base = "<?php echo '/presupuestos/lineas/sucursales/nuevo/' ?>";
            var url = base+idp+"/"+idl;
            location.href=url;
        }

         function view(idp,idl){
            var base = "<?php echo '/presupuestos/lineas/sucursales/'?>";
            var url = base+idp+"/"+idl;
            location.href=url;
        }

        function editar(idl,suc,prod,cost,cant){
            $("#id").val(idl);
            $("#sucursal").val(suc);
            $("#producto").val(prod);
            $("#costo").val(cost);
            $("#cantidad").val(cant);
            $("#cant").val(cant);
        }

        function borrar(idp,idl){
            var base = "<?php echo '/presupuestos/lineas/delete/' ?>";
            var url = base+idp+"/"+idl;
            location.href=url;
        }

        function breakup(idp,idl){
            var base = "<?php echo '/presupuestos/lineas/breakup/'?>";
            var url = base+idp+"/"+idl;
            location.href=url;
        }
    </script>

@stop