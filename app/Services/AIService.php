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
            ]);


        if ($response->failed()) {
            return null; // O manejar error
        }

        // 4. Limpieza y Decodificación
        $responseText = $response->json()['candidates'][0]['content']['parts'][0]['text'];

        // A veces la IA manda ```json ... ```, hay que limpiarlo
        $cleanJson = str_replace(['```json', '```'], '', $responseText);

        return json_decode($cleanJson, true);
    }
}
