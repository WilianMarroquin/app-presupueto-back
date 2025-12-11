<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVoiceTransactionJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VoiceCommandApiController extends Controller
{
    /**
     * Recibe el comando de voz y lo encola.
     * Respuesta < 200ms.
     */
    public function store(Request $request)
    {
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
