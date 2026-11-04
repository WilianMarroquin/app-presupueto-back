<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAssistantService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = (string) (config('services.gemini.api_key') ?: config('services.gemini.key') ?: env('GEMINI_API_KEY', ''));
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Envía el prompt del usuario junto con el snapshot financiero y el historial a Gemini.
     */
    public function sendFinancialPrompt(string $userPrompt, array $financialProfile, array $conversationHistory = []): string
    {
        if (empty($this->apiKey)) {
            return '⚠️ **Configuración pendiente:** No se encontró la clave `GEMINI_API_KEY` en el archivo `.env`. Por favor configúrala para activar tu CFO personal.';
        }

        $systemPrompt = $this->buildSystemPrompt($financialProfile);
        $contents = $this->buildContentsPayload($userPrompt, $conversationHistory);

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt],
                ],
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature'     => 0.35,
                'maxOutputTokens' => 8192,
            ],
        ];

        try {
            $response = $this->executeGeminiRequest($this->model, $payload);

            // Fallback automático si el modelo configurado (ej. gemini-1.5-flash) fue retirado en v1beta
            if ($response->status() === 404 && $this->model !== 'gemini-2.5-flash') {
                $response = $this->executeGeminiRequest('gemini-2.5-flash', $payload);
            }

            if ($response->status() === 429) {
                Log::warning('Gemini Assistant: Límite de cuota alcanzado', ['body' => $response->body()]);

                return '⏳ **Límite de consultas alcanzado temporalmente:** El servicio de IA está recibiendo demasiadas solicitudes en este momento. Por favor intenta de nuevo en unos segundos.';
            }

            if ($response->failed()) {
                Log::error('Gemini Assistant API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return '⚠️ **No fue posible completar el análisis en este momento.** Verifica tu conexión o la configuración de `GEMINI_API_KEY` e intenta nuevamente.';
            }

            $parts = data_get($response->json(), 'candidates.0.content.parts', []);
            $replyText = collect($parts)
                ->filter(fn ($part) => empty($part['thought']) && isset($part['text']))
                ->pluck('text')
                ->implode('');

            if (empty($replyText)) {
                return 'No pude generar una respuesta para esa consulta. ¿Podrías reformular tu pregunta financiera?';
            }

            return trim((string) $replyText);
        } catch (\Throwable $e) {
            Log::error('Gemini Assistant Connection Exception: ' . $e->getMessage());

            return '⚠️ **Error de conexión con el asistente financiero.** Por favor intenta nuevamente en unos instantes.';
        }
    }

    /**
     * Construye el System Prompt analítico con el perfil financiero en JSON.
     */
    protected function buildSystemPrompt(array $financialProfile): string
    {
        $profileJson = json_encode($financialProfile, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return <<<PROMPT
Eres el CFO y asesor financiero personal del usuario en su aplicación Expense Control.
Tienes acceso en tiempo real a su snapshot financiero consolidado en formato JSON que se te adjunta abajo.

Reglas de respuesta:
- Sé directo, analítico, prudente y empático. No des respuestas genéricas ni sermones vacíos.
- Cuando te pregunten si pueden comprar algo (ej. unos tenis, una laptop, etc.), evalúa su liquidez real, su disponible del período contable activo, sus compromisos fijos del mes, sus cuotas pendientes y sugiere exactamente cuánto ahorrar, en qué períodos o si conviene postergarlo.
- Utiliza siempre la moneda Quetzales (Q) y redondea a dos decimales.
- Usa formato Markdown limpio (negritas para cifras importantes y viñetas cortas).

Contexto financiero del usuario:
{$profileJson}
PROMPT;
    }

    /**
     * Mapea el historial de conversación y el mensaje actual al formato de contents de Gemini.
     */
    protected function buildContentsPayload(string $userPrompt, array $conversationHistory): array
    {
        $contents = [];

        // Limitar historial reciente para mantener foco y optimizar tokens
        $recentHistory = array_slice($conversationHistory, -12);

        foreach ($recentHistory as $message) {
            $text = trim((string) ($message['content'] ?? $message['text'] ?? ''));
            if ($text === '') {
                continue;
            }

            $rawRole = strtolower((string) ($message['role'] ?? 'user'));
            $geminiRole = in_array($rawRole, ['assistant', 'model', 'bot'], true) ? 'model' : 'user';

            $contents[] = [
                'role'  => $geminiRole,
                'parts' => [
                    ['text' => $text],
                ],
            ];
        }

        $contents[] = [
            'role'  => 'user',
            'parts' => [
                ['text' => trim($userPrompt)],
            ],
        ];

        return $contents;
    }

    /**
     * Ejecuta la petición HTTP contra el endpoint generateContent de Gemini.
     */
    protected function executeGeminiRequest(string $model, array $payload)
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";

        return Http::timeout(45)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);
    }
}
