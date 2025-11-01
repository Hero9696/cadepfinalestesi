<?php
/*
Dado que la gestión de tokens de seguridad (como JWT o de restablecimiento de contraseña) suele estar integrada en los servicios de autenticación de Laravel o paquetes específicos, este controlador se enfocará en el CRUD básico (principalmente para fines de auditoría o administración, no para el flujo de autenticación).
*/
namespace App\Http\Controllers;

use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TokenController extends Controller
{
    /**
     * Muestra una lista de tokens (solo para administradores/auditoría).
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tokens = Token::with('user')->get();
        return response()->json($tokens);
    }

    /**
     * Crea un nuevo token (normalmente usado por servicios internos).
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_user_token' => 'required|integer|exists:users,id_user',
            'idcreate_user_token' => 'required|integer|exists:users,id_user',
            'expires_in' => 'nullable|integer', // Expiración en minutos
        ]);

        $tokenValue = Str::random(60); // Generar un token aleatorio
        $expiresAt = $request->expires_in ? Carbon::now()->addMinutes($request->expires_in) : null;

        $token = Token::create([
            'id_user_token' => $request->id_user_token,
            'token_token' => hash('sha256', $tokenValue), // Guardar el hash en BD
            'created_token' => Carbon::now(),
            'expires_token' => $expiresAt,
            'active_token' => true,
            'ip_token' => $request->ip(),
            'idcreate_user_token' => $request->idcreate_user_token,
            'idupdater_user_token' => $request->idcreate_user_token,
        ]);

        // Retorna el valor plano del token para el cliente
        $token->token_token = $tokenValue;

        return response()->json($token, 201);
    }

    /**
     * Muestra un token específico.
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $token = Token::with('user')->find($id);

        if (!$token) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        // No mostrar el valor del token por seguridad, solo meta-datos
        $token->makeHidden('token_token');

        return response()->json($token);
    }

    /**
     * Invalida (actualiza) un token.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $token = Token::find($id);

        if (!$token) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        $request->validate([
            'active_token' => 'sometimes|boolean',
            'idupdater_user_token' => 'required|integer|exists:users,id_user',
        ]);

        $token->update([
            'active_token' => $request->active_token ?? false,
            'idupdater_user_token' => $request->idupdater_user_token,
        ]);

        return response()->json($token);
    }

    /**
     * Elimina un token (uso limitado).
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $token = Token::find($id);

        if (!$token) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        $token->delete();

        return response()->json(['message' => 'Token deleted successfully'], 204);
    }
}
