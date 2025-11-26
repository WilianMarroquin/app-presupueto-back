<?php

namespace App\Console\Commands;

use App\Models\InstallmentPlan;
use Illuminate\Console\Command;

class CreatePendingQuotaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-pending-quota-command';

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
        $installmentsPendings = InstallmentPlan::where('status', InstallmentPlan::STATUS_ACTIVE)
            ->get();

        foreach ($installmentsPendings as $index => $installmenPending) {
            $installmenPending->payments()->create([
                'installment_number' => $installmenPending->payments()->count() + 1,
                'month' => now()->format('m'),
                'year' => now()->format('Y'),
                'amount' => $installmenPending->monthly_fee,
                'status' => 'pending',
            ]);
        }

        return Command::SUCCESS;
    }
}
