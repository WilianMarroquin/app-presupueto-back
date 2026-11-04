<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\AssistantController;
use App\Services\FinancialProfileService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestAssistantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assistant:test
                            {prompt? : La pregunta que deseas hacerle al asistente financiero}
                            {--snapshot : Mostrar el snapshot financiero consolidado antes de enviar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando de prueba para probar AssistantController (CFO IA)';

    /**
     * Execute the console command.
     */
    public function handle(
        AssistantController $controller,
        FinancialProfileService $financialProfileService
    ): int {
        $prompt = $this->argument('prompt')
            ?? '¿Cuál es mi resumen financiero actual y cuánto puedo gastar hoy de forma segura?';

        $this->info('=== Prueba de AssistantController ===');
        $this->line('<comment>Prompt:</comment> ' . $prompt);
        $this->newLine();

        if ($this->option('snapshot')) {
            $this->info('--- Snapshot Financiero Consolidado ---');
            $snapshot = $financialProfileService->buildProfileSnapshot();
            $this->line(json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->newLine();
        }

        // Simulamos el Request con historial de ejemplo e invocamos el controlador directamente
        $request = Request::create('/api/v1/assistant/chat', 'POST', [
            'prompt'  => $prompt,
            'history' => [
                [
                    'role'    => 'user',
                    'content' => 'Hola, eres mi asesor financiero.',
                ],
                [
                    'role'    => 'assistant',
                    'content' => 'Hola. Estoy listo para analizar tus finanzas en Quetzales (Q).',
                ],
            ],
        ]);

        $this->comment('Consultando al asistente (Gemini)...');
        $startTime = microtime(true);

        $response = $controller->chat($request);
        $elapsed = round(microtime(true) - $startTime, 2);

        $statusCode = $response->getStatusCode();
        $data = $response->getData(true);

        $this->newLine();
        $this->info("--- Respuesta del Controlador (HTTP {$statusCode} en {$elapsed}s) ---");
        $this->line('<comment>Success:</comment> ' . (($data['success'] ?? false) ? 'true' : 'false'));
        $this->line('<comment>Message:</comment> ' . ($data['message'] ?? 'N/A'));
        $this->line('<comment>Timestamp:</comment> ' . ($data['data']['timestamp'] ?? 'N/A'));
        $this->newLine();
        $this->info('--- Respuesta del CFO IA ---');
        $this->output->writeln(
            $data['data']['reply'] ?? 'Sin respuesta',
            \Symfony\Component\Console\Output\OutputInterface::OUTPUT_RAW
        );
        $this->newLine();

        return self::SUCCESS;
    }
}
