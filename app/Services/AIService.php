<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        // CAMBIO AQUÍ: De 'gemini-1.5-flash' a 'gemini-2.5-flash'
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    public function categorize(string $userText, array $categories)
    {
        // 1. Preparamos el contexto (La lista de categorías existentes)
        $categoriesString = implode(', ', $categories);

        // 2. El Prompt Maestro
        $prompt = "Actúa como experto financiero.
        Clasifica el gasto: '{$userText}'.
        Usa SOLO una de estas categorías: [{$categoriesString}].
        Si ninguna encaja, usa 'General'.
        Devuelve un JSON puro sin markdown:
        {
            \"category\": \"NombreExacto\",
            \"tags\": [\"tag1\", \"tag2\", \"tag3\"] (Máximo 3 tags relevantes en minúsculas)
        }";

        // 3. Llamada a la API de Gemini
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ])->throw();;


        if ($response->failed()) {
            return null; // O manejar error
        }

        // 4. Limpieza y Decodificación
        $responseText = $response->json()['candidates'][0]['content']['parts'][0]['text'];

        // A veces la IA manda ```json ... ```, hay que limpiarlo
        $cleanJson = str_replace(['```json', '```'], '', $responseText);

        return json_decode($cleanJson, true);
    }

    public function getDailyCoach(array $context): string
    {
        $prompt = "
    Actúa como un asesor financiero personal, analítico y directo.
    Analiza el comportamiento financiero de mi usuario AYER ({$context['fecha']}) y dame un resumen estratégico (máximo 40 palabras).

    DATOS DEL USUARIO:
    - Gasto total ayer: Q.{$context['gasto_ayer']}
    - Categoría de mayor consumo: {$context['categoria_top']}
    - Meta diaria segura (Daily Safe Spend): Q.{$context['meta_diaria_segura']}
    - Presupuesto restante del mes: Q.{$context['presupuesto_restante']} para {$context['dias_restantes']} días.

    INSTRUCCIONES DE RESPUESTA:
    Genera un mensaje con esta estructura lógica:
    1. OBSERVACIÓN: Menciona cuánto gastó y en qué (si hubo gasto).
    2. ANÁLISIS: Compara brevemente con la meta diaria.
    3. RECOMENDACIÓN:
       - Si el gasto fue 0: 'Excelente estrategia de ahorro total.'
       - Si el gasto > Meta: 'Superaste tu promedio diario. Hoy te sugiero reducir gastos hormiga para compensar.'
       - Si el gasto < Meta: 'Te mantuviste bajo control. Continúa con esta disciplina.'

    Tu tono debe ser profesional, útil y alentador. Usa un emoji al final acorde al resultado.
    ";

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}?key={$this->apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ])->throw();

            return $response->json()['candidates'][0]['content']['parts'][0]['text'];

        } catch (\Exception $e) {
            \Log::error("Gemini Coach Error: " . $e->getMessage());

            // Fallbacks manuales (Por si la IA falla, mantenemos el estilo de asesor)
            if ($context['gasto_ayer'] == 0) {
                return "Ayer no registraste gastos. Es una excelente noticia para tu liquidez mensual. ¡Sigue así! 🛡️";
            }
            if ($context['gasto_ayer'] > $context['meta_diaria_segura']) {
                return "Ayer gastaste Q.{$context['gasto_ayer']}, superando tu promedio ideal. Hoy te recomiendo moderación en {$context['categoria_top']} para equilibrar. 📉";
            }
            return "Ayer gastaste Q.{$context['gasto_ayer']}, manteniéndote dentro de lo saludable. Tu presupuesto sigue estable. 🚀";
        }
    }
}
