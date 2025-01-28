@extends('adminlte::page')

@section('title', 'Proyectos')

@section('content_header')
    <h1>Proyectos</h1>
    <h3>Terminos de Pago</h3>
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
            <h5>Proyecto: {{$proyecto->nombre}}</h5>
            <h5>Cliente: {{$cliente->nombre}}</h5>
        </div>
        <div class="col-md-1">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Clave</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Termino de Pago</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            @php $name = "term".$row->producto_id;
                            @endphp
                            <th scope="row">{{$row->alias}}</th>
                            <td>{{$row->producto}}</td>
                            <td><select name="{{$name}}" id="{{$name}}">
                                @foreach ($terminos as $rowc)
                                <option value="{{$rowc->id}}" @php if ($row->terminos_id == $rowc->id) { echo "selected";} @endphp>{{$rowc->nombre}}</option>
                                @endforeach
                            </select></td>
                            <td>
                                <x-adminlte-button label="Modificar" theme="warning" icon="fas fa-info-circle" id="btneditar" onclick="edit('{{$id}}','{{$row->producto_id}}')"/>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            <table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <x-adminlte-button class="btn-sm" type="button" label="Regresar" theme="outline-danger" icon="fas fa-lg fa-trash" onclick="back()"/>
        </div>
        <div class="col-md-10">
        </div>
        <div class="col-md-1">
            <x-adminlte-button class="btn-flat" type="button" label="Autorizar" theme="info" icon="fas fa-lg fa-save" onclick="auth({{$id}})"/>
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
        function edit(id,idp){
            var tname = "#term"+idp;
            //var term = $(tname).value();
            selectElement = document.querySelector(tname);
            term = selectElement.value;
            var base = "<?php echo '/proyectos/terminos/update/'?>";
            var url = base+id+"/"+idp+"/"+term;
            location.href=url;
        }

        function auth(id){
            var base = "<?php echo '/proyectos/auth/'?>";
            var url = base+id;
            location.href=url;
        }

        function back(){
            var base = "<?php echo '/proyectos'?>";
            var url = base;
            location.href=url;
        }
    </script>
@stop