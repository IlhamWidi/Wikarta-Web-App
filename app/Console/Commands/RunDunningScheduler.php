<?php

namespace App\Console\Commands;

use App\Jobs\SendDunningNotificationJob;
use App\Models\Invoice;
use App\Models\NotificationLog;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class RunDunningScheduler extends Command
{
    protected $signature = 'dunning:run';

    protected $description = 'Generate dunning notifications for due invoices';

    public function handle(): int
    {
        $this->info('🔄 Starting dunning scheduler...');
        
        $stages = [
            'T-7' => Carbon::now()->addDays(7)->toDateString(),
            'T-3' => Carbon::now()->addDays(3)->toDateString(),
            'T-1' => Carbon::now()->addDay()->toDateString(),
            'T+3' => Carbon::now()->subDays(3)->toDateString(),
        ];

        $totalDispatched = 0;
        $totalSkipped = 0;

        foreach ($stages as $stage => $date) {
            $invoices = Invoice::with('customer')
                ->whereIn('status', ['issued', 'pending'])
                ->whereDate('due_date', $date)
                ->get();

            $this->info("📅 Stage {$stage} ({$date}): Found {$invoices->count()} invoices");

            foreach ($invoices as $invoice) {
                // Check if notification already sent for this stage
                $exists = NotificationLog::where('notification_id', $invoice->id)
                    ->where('type', 'invoice.dunning')
                    ->where('metadata->stage', $stage)
                    ->exists();

                if ($exists) {
                    $this->warn("   ⏭️  Skipped {$invoice->invoice_number} (already sent)");
                    $totalSkipped++;
                    continue;
                }

                SendDunningNotificationJob::dispatch($invoice, $stage);
                $this->info("   ✅ Dispatched {$invoice->invoice_number} to {$invoice->customer->name}");
                $totalDispatched++;
            }
        }

        $this->newLine();
        $this->info("✅ Dunning scheduler completed:");
        $this->info("   📤 Dispatched: {$totalDispatched}");
        $this->info("   ⏭️  Skipped: {$totalSkipped}");

        return self::SUCCESS;
    }
}
