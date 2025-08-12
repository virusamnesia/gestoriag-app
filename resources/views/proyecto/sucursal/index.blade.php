@extends('adminlte::page')

@section('title', 'Sucursales')

@section('content_header')
    <h1>Sucursales del proyecto</h1>
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
    </br>
    <form method="POST" action="{{ route('import.proyectos.lineas',['idp' => $idp,'idc' => $idc]) }}" enctype="multipart/form-data">
                
        @csrf

        <div class="row">
            <div class="col-md-8">
                <input type="file" name="importfile" required />
                <div class="row">
                    <x-adminlte-select2 name="tipoimport" label-class="text-lightblue"  fgroup-class="col-sm-8" required
                        igroup-size="sm" data-placeholder="Tipo de importación...">
                        <x-slot name="prependSlot">
                            <div class="input-group-text bg-gradient-info">
                                <i class="far fa-file-import"></i>
                            </div>
                        </x-slot>
                        <option/>
                        @foreach ($tipos as $row)
                        <option value="{{$row->id}}">{{$row->nombre}}</option>
                        @endforeach
                    </x-adminlte-select2>
                </div>
            </div>
            <div class="col-md-4">
                <x-adminlte-button class="btn-flat" name="btnimport" type="submit" label="Importar Proyecto" theme="info" icon="fas fa-lg fa-save"/>
            </div>
        </div>
    </form>
    </br>
    <form method="POST" action="{{ route('update.proyectos.sucursales',['idp' => $idp,'idc' => $idc]) }}" >
            
        @csrf
        <h5>Seleccionar manualmente las sucursales...</h5>
        <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-10">
                <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                    <thead class="bg-dark text-white">
                    <tr>
                        <th scope="col">Cotizar</th>
                        <th scope="col">Marca</th>
                        <th scope="col">Id Interno</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Domicilio</th>
                        <th scope="col">Municipio</th>
                        <th scope="col">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($sucursales as $row) {{-- Add here extra stylesheets --}}
                            @php $name = "sel".$row->id; @endphp
                            <tr>
                                <th scope="row">
                                    <x-adminlte-input-switch name="{{$name}}" id="{{$name}}" data-on-color="success" data-off-color="danger" />
                                </th>
                                <td>{{$row->marca}}</td>
                                <td>{{$row->id_interno}}</td>
                                <td>{{$row->sucursal}}</td>
                                <td>{{$row->domicilio}}</td>
                                <td>{{$row->municipio}}</td>
                                <td>{{$row->estado}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                <x-adminlte-button class="btn-flat" name="confirmar" type="submit" label="Confirmar" theme="info" icon="fas fa-lg fa-save"/>
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
    </script>

    <script type="text/javascript">
        function edit(id){
            var base = "<?php echo '/Proyectos/'?>";
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
            var base = "<?php echo '/Proyectos/show/'?>";
            var url = base+id;
            location.href=url;
        }

        function municipios(id){
            var base = "<?php echo '/Proyectos/municipios/'?>";
            var url = base+id;
            location.href=url;
        }
    </script>
@stop