@extends('adminlte::page')

@section('title', 'Proyecto')

@section('content_header')
    <h1>Partidas del Proyecto</h1>
    @if(Session::get('Error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!  </strong>{{  Session::get('Error'); }} @php if ($import > 0) echo '<a href="/proyectos/errores/'.$import.'">Ver errores</a>'; @endphp
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
    <h5>Proyecto: {{$proyecto->nombre}}</h5>
    <h5>Cliente: {{$cliente->nombre}}</h5>
    <div class="row">
        <div class="col-md-11">
        </div>
        <div class="col-md-1">
            <x-adminlte-button class="btn-sm" type="button" label="Cancelar" theme="outline-danger" icon="fas fa-lg fa-trash" onclick="back()"/>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                <thead class="bg-dark text-white">
                <tr>
                    <th scope="col">Sucursal</th>
                    <th scope="col">Domicilio</th>
                    <th scope="col">Municipio</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Superficie</th>
                    @foreach ($productos as $prod)
                    <th>{{$prod->producto}}</th>
                    @endforeach
                    <th scope="col">Total</th>
                </tr>
                </thead>
                <tbody>
                    @php $totalcliente = 0; $sucact =0;@endphp
                    @foreach ($lineas as $row) {{-- Add here extra stylesheets --}}
                        @if ($sucact != $row->sucursal_id)
                        @php $totalcliente = 0; $sucact = $row->sucursal_id; @endphp
                        <tr>
                            <th scope="row">{{$row->sucursal}}</th>
                            <td>{{$row->domicilio}}</td>
                            <td>{{$row->municipio}}</td>
                            <td>{{$row->estado}}</td>
                            <td>{{$row->superficie}}</td>
                            @foreach ($productos as $prod)
                                @php $valprod =0; @endphp
                                @foreach ($lineas as $lin)
                                    @if ($lin->sucursal == $row->sucursal)
                                        <td>${{number_format($lin->precio, 2)}}</td>
                                        @php $totalcliente += $lin->precio; $valprod = 1;@endphp
                                    @endif
                                @endforeach
                                @if($valprod == 0)
                                    <td>${{number_format(0, 2)}}</td>
                                @endif
                            @endforeach
                            <td>${{number_format($totalcliente, 2)}}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            <table>
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

        function back(){
            var base = "<?php echo '/proyectos'?>";
            var url = base;
            location.href=url;
        }
    </script>
@stop