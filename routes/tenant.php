<?php

declare(strict_types=1);

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ClienteController;
use App\Http\Controllers\api\CursoController;
use App\Http\Controllers\api\MatriculaController;
use App\Http\Controllers\api\TurmaController;
use App\Http\Controllers\TesteController;
use App\Http\Middleware\TokenQueryParameter;
use App\Services\Escola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return Inertia::render('welcome');
    })->name('home');
    Route::get('/teste', [ TesteController::class,'index'])->name('teste');
    // Route::get('/', function () {
    //     return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    // });
});

Route::name('api.')->prefix('api/v1')->middleware([
    'api',
    // 'auth:sanctum',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::fallback(function () {
        return view('erro404_site');
    });
    Route::get('/debug-token', function (\Illuminate\Http\Request $r) {
        $rawToken = $r->bearerToken() ?? $r->query('token');
        $tokenHash = null;
        $tokenRecord = null;

        if ($rawToken) {
            // O formato do Sanctum é: id|hash
            $parts = explode('|', $rawToken, 2);
            if (count($parts) === 2) {
                $tokenHash = hash('sha256', $parts[1]);
                $tokenRecord = \Laravel\Sanctum\PersonalAccessToken::where('id', $parts[0])
                    ->first();
            }
        }

        return response()->json([
            'bearer_token'         => $r->bearerToken(),
            'query_token'          => $r->query('token'),
            'authorization_header' => $r->header('Authorization'),
            'token_id'             => isset($parts[0]) ? $parts[0] : null,
            'token_hash_sha256'    => $tokenHash,
            'token_db_record'      => $tokenRecord ? [
                'id'             => $tokenRecord->id,
                'tokenable_type' => $tokenRecord->tokenable_type,
                'tokenable_id'   => $tokenRecord->tokenable_id,
                'name'           => $tokenRecord->name,
                'token_match'    => $tokenRecord->token === $tokenHash,
            ] : 'NOT FOUND IN DB',
            'db_connection'        => \DB::connection()->getDatabaseName(),
            'tenant'               => tenant('id'),
        ]);
    });
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/login-cliente',[AuthController::class,'loginCliente']);

    Route::get('/teste-mw', function (Request $r) {
        return response()->json([
            'middleware' => 'TESTE MW',
            'query_token' => $r->query('token'),
            'bearer' => $r->bearerToken(),
        ]);
    });

    Route::get('/teste-mw2', function (Request $r) {
        return response()->json([
            'message' => 'MIDDLEWARE TQP ONLY',
            'query_token' => $r->query('token'),
            'bearer' => $r->bearerToken(),
        ]);
    })->middleware(TokenQueryParameter::class);

    Route::middleware([TokenQueryParameter::class, 'auth:sanctum'])->group(function () {
        Route::get('/cursos', [CursoController::class, 'index']);
        Route::get('/cursos/{id}', [CursoController::class, 'show']);
        Route::post('/add-presenca-massa', [Escola::class,'add_presenca'])->name('add_presenca');

        Route::post('/cursos', [CursoController::class, 'store']);
        Route::put('/cursos/{id}', [CursoController::class, 'update']);
        Route::delete('/cursos/{id}', [CursoController::class, 'destroy']);

        Route::apiResource('turmas', TurmaController::class);
        Route::apiResource('matriculas', MatriculaController::class);
        Route::apiResource('clientes', ClienteController::class);
    });
});
