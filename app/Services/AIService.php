<?php
namespace App\Services;

use App\Models\Account;
use App\Models\TransactionCategory;
use App\Models\TransactionPaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function getDataForVoice(string $text): ?array
    {
        $today = now()->format('Y-m-d H:i:s');

        // 1. Preparar los datos con IDs para que Gemini elija
        // Usamos 'select' para enviar solo lo necesario y ahorrar tokens.
        // El formato será: [{"id":1,"name":"Comida"}, {"id":2,"name":"Gasolina"}]
        $categoriesJson = TransactionCategory::select('id', 'name')->get()->toJson();
        $accountsJson = Account::select('id', 'name', 'description')->get()->toJson();
        $paymentMethodsJson = TransactionPaymentMethod::select('id', 'name')->get()->toJson();

        $prompt = <<<EOT
    Actúa como un API JSON Parser financiero inteligente.

    CONTEXTO:
    - Fecha actual: {$today}
    - Moneda base: GTQ.
    - Input del usuario: "{$text}"

    TUS BASES DE DATOS (Selecciona el ID más apropiado basándote en el nombre/significado):
    - Categorías: {$categoriesJson}
    - Cuentas (Origen del dinero): {$accountsJson}
    - Métodos de Pago (Medio utilizado): {$paymentMethodsJson}

    INSTRUCCIONES:
    1. Analiza el texto para extraer: Monto, Moneda y Descripción.
    2. CLASIFICACIÓN (Crucial):
       - Busca en la lista de 'Categorías' la que mejor encaje semánticamente y devuelve su 'id' en `category_id`.
       - Busca en la lista de 'Cuentas' la que coincida con lo que el usuario dijo (ej: "BAC", "Cope", "Efectivo") y devuelve su 'id' en `account_id`.
       - Busca en 'Métodos de Pago' (ej: "Tarjeta", "Transferencia") y devuelve su 'id' en `payment_method_id`.
       - Si no encuentras coincidencia exacta, usa tu mejor criterio lógico para elegir el ID más cercano. Si es imposible, devuelve null en ese campo.

    REGLAS ESTRICTAS:
    - Responde SOLAMENTE con el objeto JSON.
    - Detecta si es Gasto (type='expense') o Ingreso (type='income'). Por defecto es Gasto.

    OUTPUT ESPERADO:
    {
        "amount": 150.00,
        "currency": "GTQ",
        "description": "Comida en restaurante",
        "category_id": 5,
        "account_id": 2,
        "payment_method_id": 1,
        "date": "2025-12-11 14:30:00",
        "type": "expense"
    }
    EOT;

        try {
            // 2. Llamada a la API
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->baseUrl}?key={$this->apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.0, // Precisión máxima
                        'maxOutputTokens' => 1024,
                    ]
                ])->throw();

            // 3. Extracción y Limpieza
            $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                $data = json_decode($matches[0], true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return $data;
                }
            }

            Log::error('Gemini JSON Error', ['raw' => $rawText]);
            return null;

        } catch (\Exception $e) {
            Log::error('Gemini Connection Error: ' . $e->getMessage());
            return null;
        }
    }
}
