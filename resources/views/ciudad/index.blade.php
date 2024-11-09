@extends('adminlte::page')

@section('title', 'Ciudades')

@section('content_header')
    <h1>Ciudades</h1>
@stop

@section('content')
    <form action="/ciudades/store" method="POST">
                
        @csrf
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
                <x-adminlte-input name="nombre" placeholder="Nombre"
                    fgroup-class="col-md-5" disable-feedback/>
                <x-adminlte-select2 name="municipio" label-class="text-lightblue"  fgroup-class="col-md-5"
                    igroup-size="sm" data-placeholder="Selecciona un municipio...">
                    <x-slot name="prependSlot">
                        <div class="input-group-text bg-gradient-info">
                            <i class="far fa-building"></i>
                        </div>
                    </x-slot>
                    <option/>
                    @foreach ($municipios as $rowm)
                    <option value="{{$rowm->id}}">{{$rowm->nombre}}</option>
                    @endforeach
                </x-adminlte-select2>
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
                    <th scope="col">Nombre</th>
                    <th scope="col">Municipio</th>
                    <th scope="col">Estado</th>
                    <th scope="col">País</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($ciudades as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->nombre}}</th>
                            <td>{{$row->municipio}}</td>
                            <td>{{$row->estado}}</td>
                            <td>{{$row->pais}}</td>
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
            <x-adminlte-button label="Clientes" type="button" theme="info" icon="far fa-hand-point-left" onclick="clientes()"/>
        </div>
        <div class="col-md-10">
          </div>
        <div class="col-md-1">
            <x-adminlte-button label="Municipios" type="button" theme="info" icon="fas fa-globe-americas" onclick="municipios()"/>
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
        function municipios(){
            var base = "<?php echo '/municipios' ?>";
            var url = base;
            location.href=url;
        }
    </script>

    <script type="text/javascript">
        function clientes(){
            var base = "<?php echo '/clientes/nuevo' ?>";
            var url = base;
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
@stop