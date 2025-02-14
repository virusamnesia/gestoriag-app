@extends('adminlte::page')

@section('title', 'Gestoria G')

@section('content_header')
    <h1>Bienvenido</h1>
@stop

@section('content')
    <p>Usuario de Gestoria G.</p>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
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

    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop