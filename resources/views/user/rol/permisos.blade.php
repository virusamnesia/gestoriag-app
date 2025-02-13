@extends('adminlte::page')

@section('title', 'Roles y permisos')

@section('content_header')
    <h1>Roles y permisos</h1>
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

    <div class="card">
        <div class="card-header">
            <h5>{{$rol->name}}</h5>
        </div>
        <div class="card-body">
            <h5>Lista de Permisos</h5>
            <form action="/roles/permisos/store/{{$rol->id}}" method="POST">
            
                @csrf
                
                <div class="row">
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-8">
                        <table class="table table-striped table-bordered shadow-lg mt-4" style="width:100%" id="tablarow">
                            <thead class="bg-dark text-white">
                            <tr>
                                <th scope="col">Seleccionar</th>
                                <th scope="col">Permiso</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($permisos as $row) {{-- Add here extra stylesheets --}}
                                    @php $name = "sel".$row->id; @endphp
                                    <tr>
                                        <th scope="row">
                                            <input type="checkbox" name="{{$name}}" id="{{$name}}" {{$row->check}} value="1">
                                        </th>
                                        <td>{{$row->name}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-2">
                    </div>
                </div>
                <div class="row">
                    <x-adminlte-button class="btn-flat" type="submit" label="Confirmar" theme="info" icon="fas fa-lg fa-save"/>
                </div>
            </form>
        </div>
    </div>

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
                dom: 'Bfrtip',
                paging: false,
                scrollY: 400,
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
        function nuevo(){
            var base = "<?php echo '/usuarios/roles/nuevo' ?>";
            var url = base;
            location.href=url;
        }
        
    </script>

    <script type="text/javascript">
        function permisos(id){
            var base = "<?php echo '/roles/permisos/'?>";
            var url = base+id;
            location.href=url;
        }

        function edit(id,name){
            $("#id").val(id);
            $("#enombre").val(name);
        }

    </script>
@stop