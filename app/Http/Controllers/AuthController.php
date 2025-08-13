<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){

        $credenciais = $request->only('email', 'password');

        $token = auth('api')->attempt($credenciais);

        if ($token) {
            return response()->json(['token' => $token]);
        }   

        return response()->json(['error' => 'Usuário ou senha inválido!'], 403);
        // 401 = Não autorizado
        // 403 = Proibido
    }

    public function logout(){
       auth('api')->logout();
       return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    public function refresh(){
        // Use JWTAuth facade for token refresh if using tymon/jwt-auth
        $token = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->refresh();
        return response()->json(['token' => $token]);
    }

    public function me(){
        return response()->json(auth('api')->user());
    }
}