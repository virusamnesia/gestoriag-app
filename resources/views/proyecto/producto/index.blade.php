@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Productos del proyecto</h1>
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

    <form action="/proyectos/productos/update/{{$idp}}/{{$idc}}" method="POST">
            
        @csrf
        <div class="row">
            <div class="col-md-11">
                <h5>Proyecto: {{$proyecto->nombre}}</h5>
                <h5>Cliente: {{$cliente->nombre}}</h5>
            </div>
            <div class="col-md-1">
            </div>
        </div>
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
                <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                    <thead class="bg-dark text-white">
                    <tr>
                        <th scope="col">Cotizar</th>
                        <th scope="col">Producto</th>
                        <th scope="col">Categoría</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Precio</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $row) {{-- Add here extra stylesheets --}}
                            @php $name = "sel".$row->id;
                                $cantidad = "cant".$row->id; 
                                $precio = "prec".$row->id; 
                            @endphp
                            <tr>
                                <th scope="row">
                                    <x-adminlte-input-switch name="{{$name}}" id="{{$name}}" data-on-color="success" data-off-color="danger"/>
                                </th>
                                <td>{{$row->producto}}</td> 
                                <td>{{$row->tps_nombre}}</td>
                                <td>
                                    <x-adminlte-input name="{{$cantidad}}" id="{{$cantidad}}" placeholder="$Precio" type="number" 
                                        igroup-size="m" min=0 max=100000 value="0" step="1">
                                        <x-slot name="appendSlot">
                                            <div class="input-group-text bg-light">
                                                <i class="fas fa-coins"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-input>
                                </td>
                                <td>
                                    <x-adminlte-input name="{{$precio}}" id="{{$precio}}" placeholder="$Precio" type="number" 
                                        igroup-size="lg" min=0 max=100000 value="{{$row->precio}}" step="0.01">
                                        <x-slot name="appendSlot">
                                            <div class="input-group-text bg-light">
                                                <i class="fas fa-dollar-sign"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-input>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                <table>
            </div>
            <div class="col-md-1">
            </div>
        </div>
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
            </div>
            <div class="col-md-1">
                <x-adminlte-button class="btn-flat" type="submit" label="Confirmar" theme="info" icon="fas fa-lg fa-save" onclick="limpiarFiltro('tablarow')"/>
            </div>
        </div>
    </form>

@stop

@section('plugins.BootstrapSwitch', true)

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
                paging: false,
                scrollY: 400,
                select: true,
            });
        } );
    </script>

    <script type="text/javascript">
        function nuevo(){
            var base = "<?php echo '/proyectos/nuevo' ?>";
            var url = base;
            location.href=url;
        }

        function limpiarFiltro(tableId) {
            // Obtiene la instancia del DataTable
            var table = $('#' + tableId).DataTable();
            
            // Limpia el valor del filtro y redibuja la tabla
            table.search('').draw();
        }
    </script>

    <script type="text/javascript">
        function edit(id){
            var base = "<?php echo '/proyectos/'?>";
            var url = base+id;
            location.href=url;
        }

        function cotizar(id){
            cadena="id="+ id;
            //cadena2="cotizacion="+ cotizacion+"&folioola="+folioola;

            $.ajax({
                type:"POST",
                url: "resources/proyecto/cotizar.php",
                data:cadena,
                success:function(r){
                    if(r=="Resource id #6"){
                        alertify.success("Agregado con Exito !!");
                        verDetalle(cotizacion,folioola);
                    }
                    else{
                        alertify.error("Modificación no exitosa!! "+r);
                    }
                }
                
            });
        }

        function view(id){
            var base = "<?php echo '/proyectos/show/'?>";
            var url = base+id;
            location.href=url;
        }

        function municipios(id){
            var base = "<?php echo '/proyectos/municipios/'?>";
            var url = base+id;
            location.href=url;
        }
    </script>
@stop