@extends('adminlte::page')

@section('title', 'Partida')

@section('content_header')
    <h1>Cerrar partida del Presupuesto</h1>
@stop

@section('content')
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
    <h5>Presupuesto: {{$presupuesto->nombre}}</h5>
    <h5>Proveedor: {{$proveedor->nombre}}</h5>

    <form action="/presupuestos/lineas/closeup" method="POST">
        
        @csrf

        <div class="row">
            <div class="col-md-5">
            </div>
            <div class="col-md-6">
            </div>
            <div class="col-md-1">
                <x-adminlte-button class="btn-flat" type="submit" label="Cerrar" theme="info" icon="fas fa-lg fa-save"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
                <h5>Marca: {{$linea->marca}}</h5>
                <h5>Sucursal: {{$linea->sucursal}}</h5>
                <h5>Municipio: {{$linea->municipio}}</h5>
                <h5>Estado: {{$linea->estado}}</h5>
                <h5>Producto: {{$linea->producto}}</h5>
                <h5>Importe: {{$linea->total_c}}</h5>
                <h5>Saldo: {{$linea->saldoproveedor}}</h5>
                <h5>Estatus: {{$linea->estatus}}</h5>
                @csrf
                <x-adminlte-select2 name="terminar"  label="Terminar proceso" label-class="text-red"  fgroup-class="col-md-6"
                    igroup-size="sm" data-placeholder="Terminar proceso..." required>
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="fas  fa-address-card"></i>
                        </div>
                    </x-slot>
                    <option/>
                    <option value="1" >Sí</option>
                    <option value="0" >No</option>
                </x-adminlte-select2>
                <input type="hidden" id="presupuesto" name="presupuesto" value="{{$presupuesto->id}}">
                <input type="hidden" id="linea" name="linea" value="{{$linea->id}}">
            <div class="col-md-3">
            </div>
        </div>
    </form>
    </div class="row">
    <div class="row">
        <div class="col-md-5">
        </div>
        <div class="col-md-6">
        </div>
        <div class="col-md-1">
            <x-adminlte-button class="btn-sm" type="button" label="Cancelar" theme="outline-danger" icon="fas fa-lg fa-trash" onclick="back({{$presupuesto->id}})"/>
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
        function back(id){
            var base = "<?php echo '/presupuestos/lineas/' ?>";
            var url = base+id;
            location.href=url;
        }
    </script>

@stop