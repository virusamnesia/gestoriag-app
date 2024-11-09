@extends('adminlte::page')

@section('title', 'ListaProductos')

@section('content_header')
    <h1>Prodcutos de la lista de precios</h1>
@stop

@section('content')
<h4>Lista de precios: {{$lista->nombre}}</h4>
<h5>cliente: {{$cliente->nombre}}</h5>
    <div class="row">
        <div class="col-md-2">
        </div>
        <div class="col-md-8">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Producto</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Municipio</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Costo</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $row) {{-- Add here extra stylesheets --}}
                        <tr>
                            <th scope="row">{{$row->producto}}</th>
                            <td>{{$row->tipo}}</td>
                            <td>{{$row->municipio}}</td>
                            <td>{{$row->estado}}</td>
                            <td>{{$row->precio}}</td>
                            <td>{{$row->costo}}</td>
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
            <x-adminlte-button label="Regresar" type="button" theme="info" icon="far fa-hand-point-left" onclick="back({{$idc}},{{$idl}})"/>
        </div>
        <div class="col-md-10">
        </div>
        <div class="col-md-1">
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
        function back(idc,idl){
            var base = "<?php echo '/listas/' ?>";
            var url = base+idc;
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
@stop