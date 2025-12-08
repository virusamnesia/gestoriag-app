@extends('adminlte::page')

@section('title', 'Cliente')

@section('content_header')
    <h1>Información del cliente</h1>
@stop

@section('content')
    @foreach ($cliente as $clie)
    <h4>Cliente: {{$clie->nombre}}</h4>

    <div class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-10">
        </div>
        <div class="col-md-1">
            </div>
    </div>
    <div class="row">
        <div class="col-md-3">
        </div>
        <div class="col-md-6">
            <div class="row">
                <x-adminlte-input name="clave" label="Clave" placeholder="Clave" maxlength="12" value="{{$clie->clave}}" disabled
                    fgroup-class="col-md-3" disable-feedback/>
            </div>
            <div class="row">
                <x-adminlte-input name="nombre" label="Razón Social" placeholder="Razón Social del cliente" label-class="text-lightblue" fgroup-class="col-md-12" value="{{$clie->nombre}}" disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-user text-lightblue"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="row">
                <x-adminlte-input name="rfc" label="RFC" placeholder="RFC" size="15" maxlength="15" value="{{$clie->rfc}}" disabled
                    fgroup-class="col-md-4" disable-feedback/>
            </div>
            <div class="row">
                    <x-adminlte-select2 name="posicion" label="Posición Fiscal" label-class="text-lightblue"  fgroup-class="col-md-12" disabled
                        igroup-size="sm" data-placeholder="Selecciona una Posición Fiscal...">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                        </x-slot>
                        <option/>
                        @foreach ($posiciones as $rowp)
                        <option value="{{$rowp->id}}"@php if ($rowp->id == $clie->fiscal_position_id) { echo "selected";} @endphp>{{$rowp->nombre}}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
            <div class="row">
                <x-adminlte-input name="domicilio" label="Domicilio" placeholder="Domicilio"  fgroup-class="col-md-12" value="{{$clie->domicilio}}" disabled> 
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-purple">
                            <i class="fas fa-address-card"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="row">
                <x-adminlte-input name="colonia" label="Colonia" placeholder="Colonia" fgroup-class="col-md-12" disable-feedback value="{{$clie->colonia}}" disabled/>
            </div>
            <div class="row">
                <x-adminlte-select2 name="municipio" label="Municipio" label-class="text-lightblue"  fgroup-class="col-md-12"
                    igroup-size="sm" data-placeholder="Selecciona un municipio..." disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="fas fa-location-dot"></i>
                        </div>
                    </x-slot>
                    <option/>
                    @foreach ($municipios as $rowe)

                    <option value="{{$rowe->id}}" @php if ($rowe->id == $clie->municipio_contacto_id) { echo "selected";} @endphp>{{$rowe->nombre}}, {{$rowe->estado}}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>
            <div class="row">
                <x-adminlte-input name="cp" label="Código Postal" placeholder="Código Postal"  fgroup-class="col-md-4"  maxlength="5" value="{{$clie->cp}}" disabled
                    enable-old-support>
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-olive">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="row">
                <x-adminlte-input name="email" label="E-mail" placeholder="Correo Eléctronico" label-class="text-lightblue" fgroup-class="col-md-12" value="{{$clie->email}}" disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-at text-lightblue"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="row">
                <x-adminlte-input name="telefono" label="Teléfono" placeholder="Teléfono" label-class="text-lightblue" fgroup-class="col-md-12" value="{{$clie->telefono}}" disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-phone-square-alt text-lightblue"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
        </div>
        <div class="col-md-3">
        </div>
    </div>
        
    <div class="row">
        <div class="col-md-1">
            </div>
        <div class="col-md-10">
        </div>
        <div class="col-md-1">
            <x-adminlte-button class="btn-sm" type="button" label="Cancelar" theme="outline-danger" icon="fas fa-lg fa-trash" onclick="back()"/>
        </div>
    </div>
    @endforeach

@stop

@section('plugins.Select2', true)

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
            var base = "<?php echo '/clientes' ?>";
            var url = base;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
    function delete(id){
        var base = "<?php echo '/contable/cobranza/histNotas/' ?>";
        
        var url = base;
        location.href=url;
    }
    </script>

    <script type="text/javascript">
        function edit(id){
            var base = "<?php echo '/contable/cobranza/histNotas/' ?>";
        
            var url = base;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
        function ciudades(){
            var base = "<?php echo '/ciudades' ?>";
            var url = base;
            location.href=url;
        }
    </script>
@stop