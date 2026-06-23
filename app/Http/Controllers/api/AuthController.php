<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $key = 'email';
        $credentials = [$key => $request->email, 'password' => $request->password, 'ativo' => 's', 'excluido' => 'n'];
        $logar = Auth::guard('web')->attempt($credentials, $request->filled('remember'));

        if ($logar) {
            return response()->json([
                'message' => 'Authorized',
                'status' => 200,
                'data' => [
                    'token' => $request->user()->createToken('developer')->plainTextToken,
                ],
            ]);
        }

        return response()->json(['message' => 'Sem Autorização', 'status' => 403]);
    }

    public function loginCliente(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ]);

        $cliente = Cliente::where('Email', $request->email)
            ->where('excluido', 'n')
            ->where('deletado', 'n')
            ->first();

        if (!$cliente || $cliente->senha !== $request->senha) {
            return response()->json(['message' => 'Credenciais inválidas', 'status' => 403]);
        }

        $token = $cliente->createToken('cliente')->plainTextToken;

        return response()->json([
            'message' => 'Authorized',
            'status' => 200,
            'data' => [
                'token' => $token,
                'cliente' => [
                    'id' => $cliente->id,
                    'nome' => $cliente->Nome,
                    'sobrenome' => $cliente->sobrenome,
                    'email' => $cliente->Email,
                ],
            ],
        ]);
    }
}
