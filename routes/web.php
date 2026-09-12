<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\CalculoMaterialController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN CON GOOGLE
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('google.login');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

// Mostrar página de login
Route::get('/login', function () {
    return view('login');
})->name('login');


// Procesar login tradicional
Route::post('/login', function (Request $request) {

    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Bienvenido al sistema.');
    }

    return back()
        ->withInput($request->only('email'))
        ->with(
            'error',
            'El correo electrónico o la contraseña son incorrectos.'
        );

})->name('login.process');


/*
|--------------------------------------------------------------------------
| RUTA PRINCIPAL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');

});


/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )
    ->middleware('role:admin,usuario')
    ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | PROYECTOS
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y USUARIO
    |--------------------------------------------------------------------------
    */

    Route::resource('proyectos', ProyectoController::class)
        ->middleware('role:admin,usuario');


    /*
    |--------------------------------------------------------------------------
    | GEOCODIFICACIÓN
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y USUARIO
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/proyectos/geocodificar',
        [ProyectoController::class, 'geocodificar']
    )
    ->middleware('role:admin,usuario')
    ->name('proyectos.geocodificar');


    /*
    |--------------------------------------------------------------------------
    | PLANOS
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y USUARIO
    |--------------------------------------------------------------------------
    */

    Route::resource('planos', PlanoController::class)
        ->middleware('role:admin,usuario');


    /*
    |--------------------------------------------------------------------------
    | ANALIZAR PLANO
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/planos/{plano}/analizar',
        [PlanoController::class, 'analizar']
    )
    ->middleware('role:admin,usuario')
    ->name('planos.analizar');


    /*
    |--------------------------------------------------------------------------
    | CALCULAR MATERIALES DESDE PLANO
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/planos/{plano}/calcular-materiales',
        [PlanoController::class, 'calcularMateriales']
    )
    ->middleware('role:admin,usuario')
    ->name('planos.calcular-materiales');


    /*
    |--------------------------------------------------------------------------
    | MATERIALES
    |--------------------------------------------------------------------------
    | SOLO ADMINISTRADOR
    |--------------------------------------------------------------------------
    */

    Route::resource('materials', MaterialController::class)
        ->middleware('role:admin');


    /*
    |--------------------------------------------------------------------------
    | CÁLCULOS
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/calculos',
        [CalculoMaterialController::class, 'index']
    )
    ->middleware('role:admin,usuario')
    ->name('calculos.index');


    Route::get(
        '/calculos/proyecto/{proyecto_id}',
        [CalculoMaterialController::class, 'index']
    )
    ->middleware('role:admin,usuario')
    ->name('calculos.index.proyecto');


    Route::post(
        '/calculos/calcular',
        [CalculoMaterialController::class, 'calcular']
    )
    ->middleware('role:admin,usuario')
    ->name('calculos.calcular');


    Route::delete(
        '/calculos/{calculo}',
        [CalculoMaterialController::class, 'destroy']
    )
    ->middleware('role:admin,usuario')
    ->name('calculos.destroy');


    Route::get(
        '/calculos/{plano}/reporte',
        [CalculoMaterialController::class, 'reporte']
    )
    ->middleware('role:admin,usuario')
    ->name('calculos.reporte');


    Route::get(
        '/calculos/{plano}/descargar',
        [CalculoMaterialController::class, 'descargarReporte']
    )
    ->middleware('role:admin,usuario')
    ->name('calculos.descargar');


    /*
    |--------------------------------------------------------------------------
    | REPORTES
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y USUARIO
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/reportes',
        [ReporteController::class, 'index']
    )
    ->middleware('role:admin,usuario')
    ->name('reportes.index');


    Route::get(
        '/reportes/materiales/{plano}',
        [ReporteController::class, 'materiales']
    )
    ->middleware('role:admin,usuario')
    ->name('reportes.materiales');


    Route::get(
        '/reportes/descargar/{plano}',
        [ReporteController::class, 'descargarPdf']
    )
    ->middleware('role:admin,usuario')
    ->name('reportes.descargar');


    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE USUARIOS
    |--------------------------------------------------------------------------
    | SOLO ADMINISTRADOR
    |--------------------------------------------------------------------------
    */

    Route::resource('usuarios', UsuarioController::class)
        ->middleware('role:admin');


    /*
    |--------------------------------------------------------------------------
    | CERRAR SESIÓN
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y USUARIO
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', function (Request $request) {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Sesión cerrada correctamente.');

    })->name('logout');

});