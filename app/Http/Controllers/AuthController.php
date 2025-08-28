<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function register(Request $request){
        $dados = $request->only('name', 'email', 'password');

        $validator = \Illuminate\Support\Facades\Validator::make($dados, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $dados['password'] = bcrypt($dados['password']);
        $usuario = User::create($dados);

        return response()->json($usuario, 201);
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