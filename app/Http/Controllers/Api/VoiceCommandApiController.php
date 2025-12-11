<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVoiceTransactionJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\PersonalAccessToken;

class VoiceCommandApiController extends Controller
{
    /**
     * Recibe el comando de voz y lo encola.
     * Respuesta < 200ms.
     */
    public function store(Request $request)
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json(['message' => 'Falta el Token.'], 401);
        }

        // 2. Preguntarle a Sanctum: "¿Este token es válido?"
        // findToken() busca el hash en la base de datos automáticamente.
        $token = PersonalAccessToken::findToken($bearerToken);

        if (!$token || !$token->tokenable) {
            return response()->json(['message' => 'Token Inválido o Revocado.'], 401);
        }

        // (Opcional) Actualizar la fecha de "último uso" del token
        $token->forceFill(['last_used_at' => now()])->save();

        $request->validate([
            'text' => 'required|string|min:3|max:500',
        ]);

        $texto = $request->input('text');

        ProcessVoiceTransactionJob::dispatch($texto);

        return response()->json([
            'success' => true,
            'message' => 'A la orden, procesando.',
            'timestamp' => now()->toIso8601String()
        ], 200);
    }
}
