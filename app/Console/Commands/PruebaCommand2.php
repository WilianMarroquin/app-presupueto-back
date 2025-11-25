<?php

namespace App\Console\Commands;

use App\Services\Transaction\CreateTransactionService;
use App\Services\Transaction\DOT\TransactionDTO;
use Illuminate\Console\Command;

class PruebaCommand2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prueba-command2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $datos = [
            'category_id' => 1,
            'account_id' => 1,
            'amount' => 5,
            'description' => 'Test Transaction',
            'payment_method_id' => 1,
            'is_recurring' => 0,
        ];


        $dpo = TransactionDTO::fromArray($datos);

        $respuesta = CreateTransactionService::execute($dpo);

        dd($respuesta);
    }
}
