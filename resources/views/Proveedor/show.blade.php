@extends('adminlte::page')

@section('title', 'Proveedor')

@section('content_header')
    <h1>Información del proveedor</h1>
@stop

@section('content')
    @foreach ($proveedor as $prov)
    <h4>Proveedor: {{$prov->nombre}}</h4>

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
                <x-adminlte-input name="clave" placeholder="Clave" maxlength="12" value="{{$prov->clave}}" disabled
                    fgroup-class="col-md-3" disable-feedback/>
            </div>
            <div class="row">
                <x-adminlte-input name="nombre" placeholder="Razón Social del proveedor" label-class="text-lightblue" fgroup-class="col-md-12" value="{{$prov->nombre}}" disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-user text-lightblue"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="row">
                <x-adminlte-input name="rfc" placeholder="RFC" size="15" maxlength="15" value="{{$prov->rfc}}" disabled
                    fgroup-class="col-md-4" disable-feedback/>
            </div>
            <div class="row">
                <x-adminlte-input name="domicilio" placeholder="Domicilio"  fgroup-class="col-md-12" value="{{$prov->domicilio}}" disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-purple">
                            <i class="fas fa-address-card"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="row">
                <x-adminlte-input name="colonia" placeholder="Colonia" fgroup-class="col-md-12" disable-feedback value="{{$prov->colonia}}" disabled/>
            </div>
            <div class="row">
                <x-adminlte-select2 name="municipio" label-class="text-lightblue"  fgroup-class="col-md-12"
                    igroup-size="sm" data-placeholder="Selecciona un municipio..." >
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="far fa-building"></i>
                        </div>
                    </x-slot>
                    <option/>
                    @foreach ($municipios as $rowe)

                    <option value="{{$rowe->id}}" @php if ($rowe->id == $prov->municipio_contacto_id) { echo "selected";} @endphp>{{$rowe->nombre}}, {{$rowe->estado}}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>
            <div class="row">
                <x-adminlte-input name="cp" placeholder="Código Postal"  fgroup-class="col-md-3"  maxlength="5" value="{{$prov->cp}}" disabled
                    enable-old-support>
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-olive">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="row">
                <x-adminlte-input name="email" placeholder="Correo Eléctronico" label-class="text-lightblue" fgroup-class="col-md-12" value="{{$prov->email}}"  disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-at text-lightblue"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="row">
                <x-adminlte-input name="telefono" placeholder="Teléfono" label-class="text-lightblue" fgroup-class="col-md-12" value="{{$prov->telefono}}" disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-phone-square-alt text-lightblue"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
            <div class="row">
                <x-adminlte-select2 name="banco" label-class="text-lightblue"  fgroup-class="col-md-12" disabled
                    igroup-size="sm" data-placeholder="Selecciona un banco...">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="far fa-building"></i>
                        </div>
                    </x-slot>
                    <option/>
                    @foreach ($bancos as $rowb)
                    <option value="{{$rowb->id}}" @php if ($rowb->id == $prov->banco_id) { echo "selected";} @endphp >{{$rowb->nombre}}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>
            <div class="row">
                <x-adminlte-input name="cuenta" placeholder="Cuenta bancaria" size="18" maxlength="18" value="{{$prov->cuenta}}" disabled
                    fgroup-class="col-md-4" disable-feedback/>
            </div>
        </div>
        <div class="col-md-3">
        </div>
    </div>
    @endforeach    
    <div class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-10">
        </div>
        <div class="col-md-1">
            <x-adminlte-button class="btn-sm" type="button" label="Cancelar" theme="outline-danger" icon="fas fa-lg fa-trash" onclick="back()"/>
        </div>
    </div>

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
            var base = "<?php echo '/proveedores' ?>";
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
@stop