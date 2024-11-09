@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1>Usuarios</h1>
@stop

@section('content')
    <x-adminlte-input name="iUser" label="User" placeholder="username" label-class="text-lightblue">
        <x-slot name="prependSlot">
            <div class="input-group-text">
                <i class="fas fa-user text-lightblue"></i>
            </div>
        </x-slot>
    </x-adminlte-input>

    {{-- Email type --}}
    <x-adminlte-input name="iMail" type="email" placeholder="mail@example.com"/>

    {{-- With label, invalid feedback disabled, and form group class --}}
    <div class="row">
        <x-adminlte-input name="iLabel" label="Label" placeholder="placeholder"
            fgroup-class="col-md-6" disable-feedback/>
    </div>

    {{-- With prepend slot --}}
    <x-adminlte-input name="iUser" label="User" placeholder="username" label-class="text-lightblue">
        <x-slot name="prependSlot">
            <div class="input-group-text">
                <i class="fas fa-user text-lightblue"></i>
            </div>
        </x-slot>
    </x-adminlte-input>

    {{-- With append slot, number type, and sm size --}}
    <x-adminlte-input name="iNum" label="Number" placeholder="number" type="number"
        igroup-size="sm" min=1 max=10>
        <x-slot name="appendSlot">
            <div class="input-group-text bg-dark">
                <i class="fas fa-hashtag"></i>
            </div>
        </x-slot>
    </x-adminlte-input>

    {{-- With a link on the bottom slot, and old support enabled --}}
    <x-adminlte-input name="iPostalCode" label="Postal Code" placeholder="postal code"
        enable-old-support>
        <x-slot name="prependSlot">
            <div class="input-group-text text-olive">
                <i class="fas fa-map-marked-alt"></i>
            </div>
        </x-slot>
        <x-slot name="bottomSlot">
            <a href="#">Search your postal code here</a>
        </x-slot>
    </x-adminlte-input>

    {{-- With extra information on the bottom slot --}}
    <x-adminlte-input name="iExtraAddress" label="Other Address Data">
        <x-slot name="prependSlot">
            <div class="input-group-text text-purple">
                <i class="fas fa-address-card"></i>
            </div>
        </x-slot>
        <x-slot name="bottomSlot">
            <span class="text-sm text-gray">
                [Add other address information you may consider important]
            </span>
        </x-slot>
    </x-adminlte-input>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
    {{-- <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script> --}}
@stop