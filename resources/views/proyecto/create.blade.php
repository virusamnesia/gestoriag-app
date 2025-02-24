@extends('adminlte::page')

@section('title', 'Proyecto')

@section('content_header')
    <h1>Nuevo Proyecto</h1>
@stop

@section('content')
    
    <form action="/proyectos/store" method="POST">
        
        @csrf

        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
            </div>
            <div class="col-md-1">
                <x-adminlte-button class="btn-flat" type="submit" label="Guardar" theme="info" icon="fas fa-lg fa-save"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <div class="row">
                    <x-adminlte-input name="nombre" placeholder="Nombre del proyecto"
                        fgroup-class="col-md-8" disable-feedback/>
                </div>
                <div class="row">
                    <x-adminlte-input name="año" placeholder="Año" type="number" fgroup-class="col-md-5"
                        igroup-size="sm" min=2000 max=3000 step="0.01">
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-light">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
                <div class="row">
                    <x-adminlte-select2 name="cliente" label-class="text-lightblue"  fgroup-class="col-md-10"
                        igroup-size="sm" data-placeholder="Selecciona un cliente...">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="far fa-address-card"></i>
                            </div>
                        </x-slot>
                        <option/>
                        @foreach ($clientes as $rowe)
                        <option value="{{$rowe->id}}">{{$rowe->nombre}}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
                <div class="row">
                    <x-adminlte-select2 name="agrupado" label-class="text-lightblue"  fgroup-class="col-md-8"
                        igroup-size="sm" data-placeholder="Facturación agrupada...">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="far fa-file-alt"></i>
                            </div>
                        </x-slot>
                        <option/>
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </x-adminlte-select2>
                </div>
            </div>
            <div class="col-md-3">
            </div>
        </div>
    </form>
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
            var base = "<?php echo '/Proyectos' ?>";
            var url = base;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
    function delete(id){
        var base = "<?php echo '/contable/cobranza/histNotas/' ?>";
        if (cadena>'0'){
            cad=cadena;
        }
        else{
            cad='NULL';
        }
        var url = base+cad+'/'+nombre+'/0/0/0/1';
        location.href=url;
    }
    </script>

    <script type="text/javascript">
        function edit(id){
            $("#idcadc").val(cad);
            $("#cadc").val(cad);
            $("#nacadc").val(nombre);
            $("#ncadc").val(nombre);
        }
    </script>

    <script type="text/javascript">
        function municipios(){
            var base = "<?php echo '/municipios/-1/0' ?>";
            var url = base;
            location.href=url;
        }
    </script>
@stop