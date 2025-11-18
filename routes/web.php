<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CiudadContactoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClientesFacturaController;
use App\Http\Controllers\ClientesFacturaLineaController;
use App\Http\Controllers\FiscalPositionController;
use App\Http\Controllers\ImportacionProyectoController;
use App\Http\Controllers\ImportacionProyectoProductoController;
use App\Http\Controllers\ListasPrecioController;
use App\Http\Controllers\ListasPrecioLineaController;
use App\Http\Controllers\MovimientosPagoClienteController;
use App\Http\Controllers\MovimientosPagoProveedorController;
use App\Http\Controllers\MunicipioContactoController;
use App\Http\Controllers\MunicipiosProveedorController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrecioProductoController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProductosProyectoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProveedorFacturaController;
use App\Http\Controllers\ProveedorFacturaLineaController;
use App\Http\Controllers\ProveedorMunicipioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ProyectoLineaController;
use App\Http\Controllers\ProyectoSucursalLineaController;
use App\Http\Controllers\RegimenesFiscaleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\SucursalesProyectoController;
use App\Http\Controllers\TerminosPagoClienteController;
use App\Http\Controllers\TerminosPagoProveedorController;
use App\Http\Controllers\TiposProductoController;
use App\Models\CiudadContacto;
use App\Models\ClientesFactura;
use App\Models\ClientesFacturaLinea;
use App\Models\ImportacionProyecto;
use App\Models\ImportacionProyectoProducto;
use App\Models\ListasPrecio;
use App\Models\MovimientosPagoCliente;
use App\Models\Producto;
use App\Models\ProveedorFactura;
use App\Models\ProveedorFacturaLinea;
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

    Route::get('/importaciones', [ImportacionProyectoController::class, 'index'])->name('importaciones');
    Route::post('/importaciones/nuevo', [ImportacionProyectoController::class, 'store'])->name('nuevo.importaciones');
    Route::post('/importaciones/edit', [ImportacionProyectoController::class, 'update'])->name('edit.importaciones');
    Route::get('/importaciones/productos/{id?}', [ImportacionProyectoProductoController::class, 'index'])->name('importaciones.productos');
    Route::post('/importaciones/productos/nuevo/{id?}', [ImportacionProyectoProductoController::class, 'store'])->name('nuevo.importaciones');
    Route::get('/importaciones/productos/del/{idi?}/{idp?}', [ImportacionProyectoProductoController::class, 'destroy'])->name('del.importaciones');
    
    Route::get('/tipos', [TiposProductoController::class, 'index'])->name('tipos');
    
    Route::get('/termclie', [TerminosPagoClienteController::class, 'index'])->name('termclie');
    Route::get('/termclie/nuevo', [TerminosPagoClienteController::class, 'create'])->name('new.termclie');
    Route::post('/termclie/store', [TerminosPagoClienteController::class, 'store'])->name('save.termclie');
    Route::get('/termclie/movimientos/{id?}', [MovimientosPagoClienteController::class, 'index'])->name('termclie.movimientos');
    Route::get('/termclie/movimientos/show/{id?}', [MovimientosPagoClienteController::class, 'show'])->name('termclie.show.movimientos');
    Route::get('/termclie/movimientos/edit/{id?}', [MovimientosPagoClienteController::class, 'edit'])->name('termclie.edit.movimientos');
    Route::post('/termclie/movimientos/nuevo/{id?}', [MovimientosPagoClienteController::class, 'store'])->name('save.termclie.movimientos');
    Route::post('/termclie/movimientos/{id?}', [MovimientosPagoClienteController::class, 'update'])->name('update.termclie.movimientos');

    Route::get('/posiciones', [FiscalPositionController::class, 'index'])->name('posiciones');
    Route::get('/posiciones/nuevo', [FiscalPositionController::class, 'create'])->name('new.posiciones');
    Route::post('/posiciones/store', [FiscalPositionController::class, 'store'])->name('save.posiciones');
    Route::get('/posiciones/edit', [FiscalPositionController::class, 'edit'])->name('edit.posiciones');
    Route::post('/posiciones/update', [FiscalPositionController::class, 'update'])->name('update.posiciones');
    Route::get('/posiciones/regimenes', [RegimenesFiscaleController::class, 'index'])->name('regimenes');
    Route::post('/posiciones/regimenes/store', [RegimenesFiscaleController::class, 'store'])->name('save.regimenes');
    Route::post('/posiciones/regimenes/update/{id?}', [RegimenesFiscaleController::class, 'update'])->name('update.regimenes');
        
   
    //USUARIOS
    Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios');
    Route::get('/usuarios/nuevo', [UserController::class, 'create'])->name('new.usuarios');
    Route::post('/usuarios/store', [UserController::class, 'store'])->name('save.usuarios');
    Route::get('/usuarios/show/{id?}', [UserController::class, 'show'])->name('show.usuarios');
    Route::get('/usuarios/{id?}', [UserController::class, 'edit'])->name('edit.usuarios');
    Route::post('/usuarios/update/{id?}', [UserController::class, 'update'])->name('update.usuarios');
    Route::get('/usuarios/reset/{id?}', [UserController::class, 'reset'])->name('reset.usuarios');
    Route::get('/usuarios/delete/{id?}', [UserController::class, 'destroy'])->name('reset.usuarios');
    Route::post('/usuarios/rol/store', [UserController::class, 'rol'])->name('rol.usuarios');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('store.roles');
    Route::post('/roles/update', [RoleController::class, 'update'])->name('update.roles');
    Route::get('/roles/permisos/{id?}', [RoleController::class, 'permisos'])->name('permisos.roles');
    Route::post('/roles/permisos/store/{id?}', [RoleController::class, 'storepermisos'])->name('store.permisos.roles');

    Route::get('/permisos', [PermissionController::class, 'index'])->name('permisos');
    Route::post('/permisos/store', [PermissionController::class, 'store'])->name('store.permisos');
    Route::post('/permisos/update', [PermissionController::class, 'update'])->name('update.permisos');
    
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
    Route::get('/clientes/sucursales/destroy/{idc?}/{ids?}', [SucursalController::class, 'destroy'])->name('destroy.sucursalesclientes');

    //PROVEEDORES
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores');
    Route::get('/proveedores/nuevo', [ProveedorController::class, 'create'])->name('newproveedor');
    Route::post('/proveedores/store', [ProveedorController::class, 'store'])->name('saveproveedor');
    Route::get('/proveedores/show/{id?}', [ProveedorController::class, 'show'])->name('showproveedor');
    Route::get('/proveedores/{id?}', [ProveedorController::class, 'edit'])->name('editproveedor');
    Route::post('/proveedores/update/{id?}', [ProveedorController::class, 'update'])->name('upproveedor');
    Route::get('/proveedores/municipios/{id?}', [ProveedorMunicipioController::class, 'index'])->name('munproveedor');
    Route::post('/proveedores/municipios/{id?}', [ProveedorMunicipioController::class, 'store'])->name('savemunproveedor');

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
    Route::get('/proyectos/auth/{id?}', [ProyectoController::class, 'auth'])->name('auth.proyectos');
    Route::get('/proyectos/terminos/{id?}', [ProyectoController::class, 'terminos'])->name('terminos.proyectos');
    Route::get('/proyectos/terminos/update/{id?}/{idp?}/{term?}', [ProyectoController::class, 'termupdate'])->name('update.terminos.proyectos');

    Route::get('/proyectos/sucursales/{idp?}/{idc?}', [SucursalesProyectoController::class, 'index'])->name('proyectos.sucursales');
    Route::post('/proyectos/sucursales/update/{idp?}/{idc?}', [SucursalesProyectoController::class, 'update'])->name('update.proyectos.sucursales');
    
    Route::get('/proyectos/productos/{idp?}/{idc?}', [ProductosProyectoController::class, 'index'])->name('proyectos.productos');
    Route::post('/proyectos/productos/update/{idp?}/{idc?}', [ProductosProyectoController::class, 'update'])->name('update.proyectos.productos');
    Route::get('/proyectos/matriz/{id?}', [ProyectoLineaController::class, 'matriz'])->name('proyectos.matriz');
    Route::get('/proyectos/matrizcxc/{id?}', [ProyectoLineaController::class, 'matrizcxc'])->name('proyectos.matrizcxc');
    Route::get('/proyectos/matrizsaldos/{id?}', [ProyectoLineaController::class, 'matrizsaldos'])->name('proyectos.matrizsaldos');
    Route::get('/proyectos/lineas/{id?}', [ProyectoLineaController::class, 'index'])->name('proyectos.lineas');
    Route::get('/proyectos/matrizmargen/{id?}', [ProyectoLineaController::class, 'matrizmargen'])->name('proyectos.matrizmargen');
    Route::get('/proyectos/lineas/nuevo/{id?}', [ProyectoLineaController::class, 'create'])->name('new.proyectos.lineas');
    Route::post('/proyectos/lineas/store/{id?}', [ProyectoLineaController::class, 'store'])->name('save.proyectos.lineas');
    Route::get('/proyectos/lineas/sucursales/{idp?}/{idl?}', [ProyectoSucursalLineaController::class, 'index'])->name('proyectos.lineas.sucursales');
    Route::get('/proyectos/lineas/{idp?}/{idl?}', [ProyectoLineaController::class, 'edit'])->name('edit.proyectos.lineas');
    Route::post('/proyectos/lineas/update/{idp?}/{idl?}', [ProyectoLineaController::class, 'update'])->name('update.proyectos.lineas');
    Route::get('/proyectos/lineas/sucursales/nuevo/{idp?}/{idl?}', [ProyectoSucursalLineaController::class, 'create'])->name('new.proyectos.lineas.sucursales');
    Route::post('/proyectos/lineas/sucursales/store/{idp?}/{idl?}', [ProyectoSucursalLineaController::class, 'store'])->name('save.proyectos.lineas.sucursales');
    Route::post('/proyectos/lineas/import/{idp?}/{idc?}', [ProyectoLineaController::class, 'import'])->name('import.proyectos.lineas');
    Route::get('/proyectos/errores/{id?}', [ProyectoLineaController::class, 'errores'])->name('errores.proyectos');
    Route::get('/proyectos/lineas/delete/{idp?}/{idl?}', [ProyectoLineaController::class, 'delete'])->name('delete.proyectos.lineas');
    Route::post('/proyectos/lineas/destroy/{idp?}/{idl?}', [ProyectoLineaController::class, 'destroy'])->name('destroy.proyectos.lineas');
    

    //FACTURACION CLIENTES
    Route::get('/factclientes', [ClientesFacturaController::class, 'index'])->name('factclientes');
    Route::get('/factclientes/nuevo', [ClientesFacturaController::class, 'create'])->name('new.factclientes');
    Route::get('/factclientes/previo/{id?}', [ClientesFacturaLineaController::class, 'index'])->name('factclientes.previo');
    Route::get('/factclientes/lineas/{id?}', [ClientesFacturaLineaController::class, 'lineas'])->name('factclientes.lineas');
    Route::post('/factclientes/lineas/store/{idp?}/{idc?}', [ClientesFacturaLineaController::class, 'store'])->name('save.factclientes.lineas');
    Route::get('/factclientes/lineas/odood/{id?}', [ClientesFacturaLineaController::class, 'odooDetalle'])->name('odood.factclientes.lineas');
    Route::get('/factclientes/lineas/odooa/{id?}', [ClientesFacturaLineaController::class, 'odooAgrupador'])->name('odooa.factclientes.lineas');

    //PRESUPUESTOS
    Route::get('/presupuestos', [PresupuestoController::class, 'index'])->name('presupuestos');
    Route::get('/presupuestos/nuevo', [PresupuestoController::class, 'create'])->name('new.presupuestos');
    Route::post('/presupuestos/store', [PresupuestoController::class, 'store'])->name('save.presupuestos');
    Route::get('/presupuestos/productos/{idp?}/{idv?}/{idc?}', [PresupuestoController::class, 'products'])->name('presupuestos.products');
    Route::post('/presupuestos/lineas/store/{idp?}/{idv?}/{idc?}', [PresupuestoController::class, 'storeLineas'])->name('save.presupuestos.lineas');
    Route::get('/presupuestos/{id?}', [PresupuestoController::class, 'edit'])->name('edit.presupuestos');
    Route::post('/presupuestos/update/{id?}', [PresupuestoController::class, 'update'])->name('update.presupuestos');
    Route::get('/presupuestos/auth/{id?}', [PresupuestoController::class, 'auth'])->name('auth.presupuestos');
    Route::get('/presupuestos/cancelar/{id?}', [PresupuestoController::class, 'cancelar'])->name('cancelar.presupuestos');
    Route::post('/presupuestos/costos/linea/{id?}', [PresupuestoController::class, 'updatePrice'])->name('updateprice.presupuestos.lineas');
    Route::get('/presupuestos/lineas/show/{id?}', [PresupuestoController::class, 'show'])->name('show.presupuestos');
    Route::get('/presupuestos/costos/{id?}', [PresupuestoController::class, 'costos'])->name('presupuestos.costos');
    Route::post('/presupuestos/costos/update/{id?}', [PresupuestoController::class, 'updatecostos'])->name('update.presupuestos.costos');
    Route::get('/presupuestos/lineas/{id?}', [PresupuestoController::class, 'lineas'])->name('presupuestos.lineas');
    Route::post('/presupuestos/lineas/costo/{id?}', [PresupuestoController::class, 'updatecosto'])->name('update.presupuestos.lineas');
    Route::get('/presupuestos/lineas/sucursales/{idp?}/{idl?}', [PresupuestoController::class, 'indexmov'])->name('presupuestos.lineas.sucursales');
    Route::get('/presupuestos/lineas/delete/{idp?}/{idl?}', [PresupuestoController::class, 'destroy'])->name('delete.presupuestos.lineas.');
    Route::get('/presupuestos/lineas/sucursales/nuevo/{idp?}/{idl?}', [PresupuestoController::class, 'createmov'])->name('new.presupuestos.lineas.sucursales');
    Route::post('/presupuestos/lineas/sucursales/store/{idp?}/{idl?}', [PresupuestoController::class, 'storemov'])->name('save.presupuestos.lineas.sucursales');
    Route::get('/presupuestos/matriz/{id?}', [PresupuestoController::class, 'matriz'])->name('presupuestos.matriz');
    Route::get('/presupuestos/matrizcxp/{id?}', [PresupuestoController::class, 'matrizcxp'])->name('presupuestos.matrizcxc');
    Route::get('/presupuestos/matrizsaldos/{id?}', [PresupuestoController::class, 'matrizsaldos'])->name('presupuestos.matrizsaldos');
    
    //FACTURACION PROVEEDORES
    Route::get('/factproveedores', [ProveedorFacturaController::class, 'index'])->name('factproveedores');
    Route::get('/factproveedores/nuevo', [ProveedorFacturaController::class, 'create'])->name('new.factproveedores');
    Route::get('/factproveedores/previo/{id?}', [ProveedorFacturaLineaController::class, 'index'])->name('factproveedores.previo');
    Route::get('/factproveedores/lineas/{id?}', [ProveedorFacturaLineaController::class, 'lineas'])->name('factproveedores.lineas');
    Route::post('/factproveedores/lineas/store/{idp?}/{idc?}', [ProveedorFacturaLineaController::class, 'store'])->name('save.factproveedores.lineas');

});


