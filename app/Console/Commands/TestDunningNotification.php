<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use App\Jobs\SendDunningNotificationJob;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class TestDunningNotification extends Command
{
    protected $signature = 'dunning:test {email} {phone}';

    protected $description = 'Create test customer & invoice, then send dunning notification';

    public function handle(): int
    {
        $email = $this->argument('email');
        $phone = $this->argument('phone');

        // Anti-spam protection: Confirm before sending
        if (!$this->confirm('⚠️  Ini akan mengirim WhatsApp & Email ke customer. Lanjutkan?', true)) {
            $this->warn('❌ Test dibatalkan');
            return self::FAILURE;
        }

        $this->info('🔧 Membuat customer test...');

        // Check if customer exists
        $existingCustomer = Customer::where('phone', $phone)->orWhere('email', $email)->first();
        
        if ($existingCustomer) {
            $this->info("✅ Menggunakan customer existing: {$existingCustomer->name} (ID: {$existingCustomer->id})");
            $this->info("   Email: {$existingCustomer->email}");
            $this->info("   Phone: {$existingCustomer->phone}");
            $customer = $existingCustomer;
        } else {
            // Create test customer
            $customer = Customer::create([
                'name' => 'Test Customer Dunning',
                'email' => $email,
                'phone' => $phone,
                'address' => 'Jl. Test Dunning No. 123',
                'city' => 'Jakarta',
                'postal_code' => '12345',
                'status' => 'active',
                'notes' => 'Customer test untuk dunning notification'
            ]);

            $this->info("✅ Customer created: {$customer->name} (ID: {$customer->id})");
        }

        // Get or create package
        $package = Package::first();
        if (!$package) {
            $package = Package::create([
                'name' => 'Test Package',
                'slug' => 'test-package',
                'description' => 'Package untuk testing',
                'price' => 150000,
                'speed' => '10 Mbps',
                'quota' => 'Unlimited',
                'duration_days' => 30,
                'is_active' => true
            ]);
        }

        $this->info('🧾 Membuat invoice test...');

        // Create test invoice (due in 3 days - T-3)
        $dueDate = Carbon::now()->addDays(3);
        
        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'invoice_date' => Carbon::now(),
            'due_date' => $dueDate,
            'subtotal' => $package->price,
            'tax' => 0,
            'discount' => 0,
            'total' => $package->price,
            'status' => 'issued',
            'notes' => 'Invoice test untuk dunning notification'
        ]);

        // Add invoice items
        $invoice->items()->create([
            'package_id' => $package->id,
            'description' => $package->name,
            'quantity' => 1,
            'unit_price' => $package->price,
            'subtotal' => $package->price
        ]);

        $this->info("✅ Invoice created: {$invoice->invoice_number}");
        $this->info("   Due Date: {$dueDate->format('d M Y')}");
        $this->info("   Total: Rp " . number_format($invoice->total, 0, ',', '.'));

        $this->info('');
        $this->info('📱 Mengirim dunning notification (T-3)...');

        // Dispatch dunning notification job
        SendDunningNotificationJob::dispatch($invoice, 'T-3');

        $this->info('✅ Job dispatched! Cek:');
        $this->info("   - Email: {$email}");
        $this->info("   - WhatsApp: {$phone}");
        $this->info('   - Database: notifications table');

        $this->newLine();
        $this->info('💡 Untuk process job, run:');
        $this->warn('   php artisan queue:work');

        return self::SUCCESS;
    }
}
