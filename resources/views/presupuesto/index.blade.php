@extends('adminlte::page')

@section('title', 'Presupuestos')

@section('content_header')
    <h1>Presupuestos</h1>
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
                    <th scope="col">Presupesto</th>
                    <th scope="col">Proveedor</th>
                    <th scope="col">Posición Fiscal</th>
                    <th scope="col">Año</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">IVA Trasladado</th>
                    <th scope="col">ISR Retenido</th>
                    <th scope="col">IVA Retenido</th>
                    <th scope="col">Impuesto Cedular</th>
                    <th scope="col">Total</th>
                    <th scope="col">Saldo</th>
                    <th scope="col">CxP</th>
                    <th scope="col">Pagado</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Cotización</th>
                    <th scope="col">Autorización</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($presupuestos as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->nombre}}</th>
                            <td>{{$row->proveedor}}</td>
                            <td>{{$row->posicion}}</td>
                            <td>{{$row->anio}}</td>
                            <td>${{number_format($row->subtotal, 2)}}</td>
                            <td>${{number_format($row->iva_t, 2)}}</td>
                            <td>${{number_format($row->isr_r, 2)}}</td>
                            <td>${{number_format($row->iva_r, 2)}}</td>
                            <td>${{number_format($row->imp_c, 2)}}</td>
                            <td>${{number_format($row->importe, 2)}}</td>
                            <td>${{number_format($row->saldo, 2)}}</td>
                            <td>${{number_format($row->cxp, 2)}}</td>
                            <td>${{number_format($row->importe-$row->saldo-$row->cxp, 2)}}</td>
                            <td>{{$row->estado}}</td>
                            <td>{{$row->fecha_cotizacion}}</td>
                            <td>{{$row->fecha_autorizacion}}</td>
                            <td>
                                <span class="pull-right">
                                    <div class="dropdown">
                                        <button class="btn btn-grey dropdown-toggle" type="button" id="dropdownmenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones<span class="caret"></span></button>
                                        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownmenu1">
                                            @if($row->estados_presupuesto_id != 6)                    
                                            <li><button class="btn align-self-left" id="btnedit"  onclick="edit({{$row->id}})"><i class="icon ion-md-create"></i>Líneas</button></li>
                                            <li><button class="btn align-self-left" id="btnview" onclick="view({{$row->id}})"><i class="ion-md-chatboxes"></i>Ver</button></li>
                                            @if($permisom)
                                            <li><button class="btn align-self-left" id="btnmatriz" onclick="matriz({{$row->id}})"><i class="ion-md-chatboxes"></i>Matriz</button></li>
                                            <li><button class="btn align-self-left" id="btnmatrizcxc" onclick="matrizcxc({{$row->id}})"><i class="ion-md-chatboxes"></i>Matriz CxC</button></li>
                                            <li><button class="btn align-self-left" id="btnmatrizsaldos" onclick="matrizsaldos({{$row->id}})"><i class="ion-md-chatboxes"></i>Matriz Saldos</button></li>
                                            <li><button class="btn align-self-left" id="btnpdford" onclick="pdforden({{$row->id}})"><i class="ion-md-chatboxes"></i>Orden PDF</button></li>
                                            @endif
                                            @if($permisoa)
                                            @if($row->autorizar== 0)
                                            <li><button class="btn align-self-left" id="btncostos" onclick="costos({{$row->id}})"><i class="ion-md-chatboxes"></i>Costos</button></li>
                                            <li><button class="btn align-self-left" id="btnauth" onclick="auth({{$row->id}})"><i class="ion-md-chatboxes"></i>Autorizar</button></li>
                                            <li><button class="btn align-self-left" data-toggle="modal" data-target="#smeditar" id="btnpos" onclick="fpos('{{$row->id}}','{{$row->posicion}}','{{$row->proveedor}}','{{$row->nombre}}')"><i class="ion-md-chatboxes"></i>Posición</button></li>
                                            @endif
                                            @endif
                                            <li><button class="btn align-self-left" id="btncancelar" onclick="cancelar({{$row->id}})"><i class="icon ion-md-albums"></i>Cancelar</button></li>
                                            @endif
                                            @if($row->estados_presupuesto_id == 6)                    
                                            <li><button class="btn align-self-left" id="btndestroy"  onclick="destroy({{$row->id}})"><i class="icon ion-md-create"></i>Eliminar</button></li>
                                            @endif
                                    </div>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            <table>
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
                <form class="form p-3" action="/presupuestos/posiciones" method="POST">
                    @csrf
                    <div class="modal-body">
                        <br>
                        <h6>Editar Posición Fiscal</h6>
                        <br>
                        <div class="row">
                            <input type="hidden" name="id" id="id" />
                            <x-adminlte-input name="presupuesto" label="Presupuesto" id="presupuesto" placeholder="Nombre del presupuesto" label-class="text-lightblue" 
                            fgroup-class="col-md-12" disabled>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-user text-lightblue"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="proveedor" id="proveedor" label="Proveedor" placeholder="Proveedor" label-class="text-lightblue" 
                            fgroup-class="col-md-12" disabled>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-user text-lightblue"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-input name="fiscal" id="fiscal" label="Posición Fiscal" placeholder="Posición fiscal del presupuesto" label-class="text-lightblue" 
                            fgroup-class="col-md-12" disabled>
                                <x-slot name="prependSlot">
                                    <div class="input-group-text">
                                        <i class="fas fa-user text-lightblue"></i>
                                    </div>
                                </x-slot>
                            </x-adminlte-input>
                        </div>
                        <div class="row">
                            <x-adminlte-select2 name="posicion" label="Nueva posición Fiscal" label-class="text-lightblue"  fgroup-class="col-md-10"
                                igroup-size="sm" data-placeholder="Nueva posición fiscal...">
                                <x-slot name="prependSlot">
                                    <div class="input-group-text bg-gradient-info">
                                        <i class="far fa-address-card"></i>
                                    </div>
                                </x-slot>
                                <option/>
                                @foreach ($posiciones as $rowp)
                                <option value="{{$rowp->id}}">{{$rowp->nombre}}</option>
                                @endforeach
                            </x-adminlte-select2>
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
            var base = "<?php echo '/presupuestos/nuevo'?>";
            var url = base;
            location.href=url;
        }

        function cancelar(id){
            var base = "<?php echo '/presupuestos/cancelar/'?>";
            var url = base+id;
            location.href=url;
        }

        function destroy(id){
            var base = "<?php echo '/presupuestos/eliminar/'?>";
            var url = base+id;
            location.href=url;
        }

        function fpos(id,pos,prov,pres){
            $("#id").val(id);
            $("#fiscal").val(pos);
            $("#presupuesto").val(pres);
            $("#proveedor").val(prov);
        }
    </script>

    <script type="text/javascript">
        function edit(id){
            var base = "<?php echo '/presupuestos/lineas/'?>";
            var url = base+id;
            location.href=url;
        }

        function view(id){
            var base = "<?php echo '/presupuestos/show/'?>";
            var url = base+id;
            location.href=url;
        }

        function costos(id){
            var base = "<?php echo '/presupuestos/costos/'?>";
            var url = base+id;
            location.href=url;
        }

        function auth(id){
            var base = "<?php echo '/presupuestos/auth/'?>";
            var url = base+id;
            location.href=url;
        }

        function matriz(id){
            var base = "<?php echo '/presupuestos/matriz/'?>";
            var url = base+id;
            location.href=url;
        }

        function matrizcxc(id){
            var base = "<?php echo '/presupuestos/matrizcxc/'?>";
            var url = base+id;
            location.href=url;
        }

        function matrizsaldos(id){
            var base = "<?php echo '/presupuestos/matrizsaldos/'?>";
            var url = base+id;
            location.href=url;
        }

        function pdforden(id){
            var base = "<?php echo '/presupuestos/pdf/ordencompra/'?>";
            var url = base+id;
            location.href=url;
        }
    </script>
@stop