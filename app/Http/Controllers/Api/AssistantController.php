<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Services\FinancialProfileService;
use App\Services\GeminiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssistantController extends AppBaseController
{
    public function __construct(
        protected FinancialProfileService $financialProfileService,
        protected GeminiAssistantService $geminiAssistantService
    ) {
    }

    /**
     * Procesa una consulta conversacional del usuario hacia el Asistente Financiero (CFO IA).
     * POST /api/v1/assistant/chat
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt'            => ['required', 'string', 'max:3000'],
            'history'           => ['nullable', 'array', 'max:30'],
            'history.*.role'    => ['required_with:history', 'string', 'in:user,assistant,model'],
            'history.*.content' => ['required_with:history', 'string'],
        ]);

        $financialProfile = $this->financialProfileService->buildProfileSnapshot();

        $reply = $this->geminiAssistantService->sendFinancialPrompt(
            userPrompt: $validated['prompt'],
            financialProfile: $financialProfile,
            conversationHistory: $validated['history'] ?? []
        );

        return response()->json([
            'success' => true,
            'message' => 'Respuesta generada',
            'data'    => [
                'reply'     => $reply,
                'timestamp' => now()->format('H:i'),
            ],
        ]);
    }
}
