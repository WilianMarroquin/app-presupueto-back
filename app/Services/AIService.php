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
    Actúa como un entrenador financiero personal sarcástico pero motivador.
    Analiza el comportamiento financiero de mi usuario AYER ({$context['fecha']}) y dale una frase corta (máx 25 palabras).

    DATOS:
    - Gasto ayer: Q.{$context['gasto_ayer']}
    - En qué gastó más: {$context['categoria_top']}
    - Meta diaria ideal: Q.{$context['meta_diaria_segura']}
    - Le quedan Q.{$context['presupuesto_restante']} para {$context['dias_restantes']} días.

    REGLAS:
    1. Si 'Gasto ayer' es 0: Felicítalo efusivamente. Usa emoji 🛡️.
    2. Si 'Gasto ayer' > 'Meta diaria ideal': Regáñalo suavemente mencionando la categoría culpable. Usa emoji 📉.
    3. Si 'Gasto ayer' < 'Meta diaria ideal': Motívalo a seguir así. Usa emoji 🚀.
    4. Sé breve, directo y usa jerga guatemalteca muy leve si aplica (opcional).

    Responde SOLO con el texto del consejo.
    ";



        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}?key={$this->apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ])->throw();

            return $response->json()['candidates'][0]['content']['parts'][0]['text'];

        } catch (\Exception $e) {
            \Log::error("Gemini Coach Error: " . $e->getMessage());
            // Fallback si la IA falla (Plan B manual)
            if ($context['gasto_ayer'] == 0) return "¡Ayer no gastaste nada! Sigue así campeón. 🛡️";
            if ($context['gasto_ayer'] > $context['meta_diaria_segura']) return "Te pasaste ayer. Hoy toca apretarse el cincho. 📉";
            return "Vas bien. Mantén el ritmo hoy. 🚀";
        }
    }
}
