@extends('adminlte::page')

@section('title', 'ListaProductos')

@section('content_header')
    <h1>Productos de la lista de precios</h1>
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
    <h4>Lista de precios: {{$lista->nombre}}</h4>
    <h5>cliente: {{$cliente->nombre}}</h5>

    <form action="/listasprecio/productos/store/{{$idc}}/{{$idl}}" method="POST">
            
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <x-adminlte-select2 name="producto" label-class="text-lightblue"  fgroup-class="col-md-5"
                        igroup-size="sm" data-placeholder="Selecciona un producto...">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="fab fa-product-hunt"></i>
                            </div>
                        </x-slot>
                        <option/>
                        @foreach ($productos_all as $rowp)
                        <option value="{{$rowp->id}}">{{$rowp->nombre}}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <x-adminlte-select2 name="municipio" label-class="text-lightblue"  fgroup-class="col-md-5"
                        igroup-size="sm" data-placeholder="Selecciona un municipio...">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="far fa-building"></i>
                            </div>
                        </x-slot>
                        <option/>
                        @foreach ($municipios as $rowe)
                        <option value="{{$rowe->id}}">{{$rowe->nombre}}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <x-adminlte-input name="precio" placeholder="$Precio" type="number" fgroup-class="col-md-5"
                        igroup-size="sm" min=1 max=1000>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-light">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
                <div class="row">
                    <x-adminlte-input name="costo" placeholder="$Costo" type="number" fgroup-class="col-md-5"
                        igroup-size="sm" min=1 max=1000>
                        <x-slot name="appendSlot">
                            <div class="input-group-text bg-light">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </x-slot>
                    </x-adminlte-input>
                </div>
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