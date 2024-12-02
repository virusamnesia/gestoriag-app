<?php

use App\Http\Controllers\backend\UserController;
use App\Http\Controllers\CiudadContactoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ListasPrecioController;
use App\Http\Controllers\ListasPrecioLineaController;
use App\Http\Controllers\MovimientosPagoClienteController;
use App\Http\Controllers\MovimientosPagoProveedorController;
use App\Http\Controllers\MunicipioContactoController;
use App\Http\Controllers\MunicipiosProveedorController;
use App\Http\Controllers\PrecioProductoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProductosProyectoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ProyectoLineaController;
use App\Http\Controllers\ProyectoSucursalLineaController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\SucursalesProyectoController;
use App\Http\Controllers\TerminosPagoClienteController;
use App\Http\Controllers\TerminosPagoProveedorController;
use App\Http\Controllers\TiposProductoController;
use App\Models\CiudadContacto;
use App\Models\ListasPrecio;
use App\Models\MovimientosPagoCliente;
use App\Models\Producto;
use App\Models\ProyectoLinea;
use App\Models\ProyectoSucursalLinea;
use App\Models\TiposProducto;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    //CONFIGURACION
    Route::get('/municipios/{idc?}/{ids?}', [MunicipioContactoController::class, 'index'])->name('municipios');
    Route::post('/municipios/store/{idc?}/{ids?}', [MunicipioContactoController::class, 'store'])->name('savemunicipios');
    Route::get('/ciudades', [CiudadContactoController::class, 'index'])->name('ciudades');
    Route::post('/ciudades/store', [CiudadContactoController::class, 'store'])->name('saveciudades');

    Route::get('/listas/{id?}', [ListasPrecioController::class, 'index'])->name('listas');
    Route::get('/listas/nuevo/{id?}', [ListasPrecioController::class, 'create'])->name('newlista');
    Route::get('/listas/productos/{idc?}/{idl?}', [ListasPrecioLineaController::class, 'index'])->name('listas.productos');
    
    Route::get('/tipos', [TiposProductoController::class, 'index'])->name('tipos');
    
    Route::get('/termclie', [TerminosPagoClienteController::class, 'index'])->name('termclie');
    Route::get('/termclie/nuevo', [TerminosPagoClienteController::class, 'create'])->name('new.termclie');
    Route::post('/termclie/nuevo', [TerminosPagoClienteController::class, 'store'])->name('save.termclie');
    Route::get('/termclie/movimientos/{id?}', [MovimientosPagoClienteController::class, 'index'])->name('termclie.movimientos');
    Route::get('/termclie/movimientos/show/{id?}', [MovimientosPagoClienteController::class, 'show'])->name('termclie.show.movimientos');
    Route::get('/termclie/movimientos/edit/{id?}', [MovimientosPagoClienteController::class, 'edit'])->name('termclie.edit.movimientos');
    Route::post('/termclie/movimientos/{id?}', [MovimientosPagoClienteController::class, 'store'])->name('save.termclie.movimientos');
    
    Route::get('/termprov', [TerminosPagoProveedorController::class, 'index'])->name('termprov');
    Route::get('/termprov/nuevo', [TerminosPagoProveedorController::class, 'create'])->name('new.termprov');
    Route::post('/termprov/nuevo', [TerminosPagoProveedorController::class, 'store'])->name('save.termprov');
    Route::get('/termprov/movimientos/{id?}', [MovimientosPagoProveedorController::class, 'index'])->name('termprov.movimientos');
    Route::get('/termprov/movimientos/edit/{id?}', [MovimientosPagoProveedorController::class, 'edit'])->name('edit.termprov.movimientos');
    Route::get('/termprov/movimientos/show/{id?}', [MovimientosPagoProveedorController::class, 'show'])->name('show.termprov.movimientos');
    Route::post('/termprov/movimientos/{id?}', [MovimientosPagoProveedorController::class, 'store'])->name('save.termprov.movimientos');

    //USUARIOS
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios');
    
    //CLIENTES
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes');
    Route::get('/clientes/nuevo', [ClienteController::class, 'create'])->name('newcliente');
    Route::post('/clientes/store', [ClienteController::class, 'store'])->name('savecliente');
    Route::get('/clientes/show/{id?}', [ClienteController::class, 'show'])->name('showcliente');
    Route::get('/clientes/{id?}', [ClienteController::class, 'edit'])->name('editcliente');
    Route::post('/clientes/update/{id?}', [ClienteController::class, 'update'])->name('upcliente');

    Route::get('/clientes/sucursales/{id?}', [SucursalController::class, 'indexcliente'])->name('sucursalesclientes');
    Route::get('/clientes/sucursales/nuevo/{id?}', [SucursalController::class, 'create'])->name('newsucursalclientes');
    Route::post('/clientes/sucursales/store/{id?}', [SucursalController::class, 'store'])->name('savesucursalclientes');
    Route::get('/clientes/sucursales/show/{idc?}/{ids?}', [SucursalController::class, 'show'])->name('showsucursalclientes');
    Route::get('/clientes/sucursales/{idc?}/{ids?}', [SucursalController::class, 'edit'])->name('editsucursalclientes');
    Route::post('/clientes/sucursales/{idc?}/{ids?}', [SucursalController::class, 'update'])->name('upsucursalclientes');

    //PROVEEDORES
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores');
    Route::get('/proveedores/nuevo', [ProveedorController::class, 'create'])->name('newproveedor');
    Route::post('/proveedores/store', [ProveedorController::class, 'store'])->name('saveproveedor');
    Route::get('/proveedores/show/{id?}', [ProveedorController::class, 'show'])->name('showproveedor');
    Route::get('/proveedores/{id?}', [ProveedorController::class, 'edit'])->name('editproveedor');
    Route::post('/proveedores/update/{id?}', [ProveedorController::class, 'update'])->name('upproveedor');
    Route::get('/proveedores/municipios/{id?}', [MunicipiosProveedorController::class, 'index'])->name('munproveedor');
    Route::post('/proveedores/municipios/{id?}', [MunicipiosProveedorController::class, 'store'])->name('savemunproveedor');

    //PRODUCTOS
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos');
    Route::get('/productos/nuevo', [ProductoController::class, 'create'])->name('newproducto');
    Route::post('/productos/store', [ProductoController::class, 'store'])->name('saveproducto');
    Route::get('/productos/show/{id?}', [ProductoController::class, 'show'])->name('showproducto');
    Route::get('/productos/{id?}', [ProductoController::class, 'edit'])->name('editproducto');
    Route::post('/productos/update/{id?}', [ProductoController::class, 'update'])->name('upproducto');

    //PROYECTOS
    Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos');
    Route::get('/proyectos/nuevo', [ProyectoController::class, 'create'])->name('new.proyectos');
    Route::post('/proyectos/store', [ProyectoController::class, 'store'])->name('save.proyectos');
    Route::get('/proyectos/show/{id?}', [ProyectoController::class, 'show'])->name('show.proyectos');
    Route::get('/proyectos/{id?}', [ProyectoController::class, 'edit'])->name('edit.proyectos');
    Route::post('/proyectos/update/{id?}', [ProyectoController::class, 'update'])->name('update.proyectos');

    Route::get('/proyectos/sucursales/{idp?}/{idc?}', [SucursalesProyectoController::class, 'index'])->name('proyectos.sucursales');
    Route::post('/proyectos/sucursales/update/{idp?}/{idc?}', [SucursalesProyectoController::class, 'update'])->name('update.proyectos.sucursales');
    
    Route::get('/proyectos/productos/{idp?}/{idc?}', [ProductosProyectoController::class, 'index'])->name('proyectos.productos');
    Route::post('/proyectos/productos/update/{idp?}/{idc?}', [ProductosProyectoController::class, 'update'])->name('update.proyectos.productos');

    Route::get('/proyectos/lineas/{id?}', [ProyectoLineaController::class, 'index'])->name('proyectos.lineas');
    Route::get('/proyectos/lineas/nuevo/{id?}', [ProyectoLineaController::class, 'create'])->name('new.proyectos.lineas');
    Route::post('/proyectos/lineas/store/{id?}', [ProyectoLineaController::class, 'store'])->name('save.proyectos.lineas');
    Route::get('/proyectos/lineas/sucursales/{idp?}/{idl?}', [ProyectoSucursalLineaController::class, 'index'])->name('proyectos.lineas.sucursales');
    Route::get('/proyectos/lineas/{idp?}/{idl?}', [ProyectoLineaController::class, 'edit'])->name('edit.proyectos.lineas');
    Route::post('/proyectos/lineas/update/{idp?}/{idl?}', [ProyectoLineaController::class, 'update'])->name('update.proyectos.lineas');
    Route::get('/proyectos/lineas/sucursales/nuevo/{idp?}/{idl?}', [ProyectoSucursalLineaController::class, 'create'])->name('new.proyectos.lineas.sucursales');
    Route::post('/proyectos/lineas/sucursales/store/{idp?}/{idl?}', [ProyectoSucursalLineaController::class, 'store'])->name('save.proyectos.lineas.sucursales');
    

});


