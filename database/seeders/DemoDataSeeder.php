<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\SupportTicket;
use App\Models\Installation;
use App\Models\Giveaway;
use App\Models\GiveawayClaim;
use App\Models\Attendance;
use App\Models\NotificationLog;
use App\Models\CmsPage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding demo data...');

        // Get users
        $admin = User::where('email', 'admin@agusprovider.com')->first();
        $teknisi = User::where('email', 'teknisi@agusprovider.com')->first();

        // 1. Create Packages (6 packages)
        $this->command->info('📦 Creating packages...');
        
        if (Package::count() == 0) {
            $packages = [
            [
                'name' => 'Paket Basic 10 Mbps',
                'slug' => 'basic-10mbps',
                'speed_mbps' => 10,
                'price' => 150000,
                'installation_fee' => 250000,
                'description' => 'Paket internet basic untuk kebutuhan browsing dan streaming standar',
                'features' => ['10 Mbps download/upload', 'Unlimited quota', 'Free instalasi*', '24/7 support'],
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Paket Premium 20 Mbps',
                'slug' => 'premium-20mbps',
                'speed_mbps' => 20,
                'price' => 250000,
                'installation_fee' => 250000,
                'description' => 'Paket internet premium untuk keluarga dengan banyak device',
                'features' => ['20 Mbps download/upload', 'Unlimited quota', 'Free instalasi*', 'Priority support', 'Free router'],
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Paket Ultra 50 Mbps',
                'slug' => 'ultra-50mbps',
                'speed_mbps' => 50,
                'price' => 400000,
                'installation_fee' => 300000,
                'description' => 'Paket internet super cepat untuk gaming dan streaming 4K',
                'features' => ['50 Mbps download/upload', 'Unlimited quota', 'Free instalasi*', 'VIP support', 'Free premium router', 'Static IP'],
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Paket Business 100 Mbps',
                'slug' => 'business-100mbps',
                'speed_mbps' => 100,
                'price' => 750000,
                'installation_fee' => 500000,
                'description' => 'Paket untuk kebutuhan bisnis dan kantor',
                'features' => ['100 Mbps dedicated', 'Unlimited quota', 'Free instalasi', 'Dedicated support', 'Static IP', 'SLA 99.9%'],
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Paket Lite 5 Mbps',
                'slug' => 'lite-5mbps',
                'speed_mbps' => 5,
                'price' => 99000,
                'installation_fee' => 200000,
                'description' => 'Paket hemat untuk kebutuhan internet ringan',
                'features' => ['5 Mbps download/upload', 'Unlimited quota', 'Free instalasi*', 'Standard support'],
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Paket Gaming 75 Mbps',
                'slug' => 'gaming-75mbps',
                'speed_mbps' => 75,
                'price' => 550000,
                'installation_fee' => 300000,
                'description' => 'Paket khusus gamer dengan low latency',
                'features' => ['75 Mbps download/upload', 'Unlimited quota', 'Low latency', 'Gaming VPN', 'Free premium router', 'Priority routing'],
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($packages as $packageData) {
            Package::create($packageData);
        }
        } else {
            $this->command->info('   Packages already exist, skipping...');
        }

        // 2. Create Customers (100 customers)
        $this->command->info('👥 Creating customers...');
        $customerData = [];
        $indonesianNames = [
            'Budi Santoso', 'Siti Nurhaliza', 'Ahmad Yani', 'Dewi Lestari', 'Agus Salim',
            'Rina Wijaya', 'Bambang Susilo', 'Maya Sari', 'Hendro Gunawan', 'Lina Marlina',
            'Rudi Hartono', 'Sri Mulyani', 'Eko Prasetyo', 'Indah Permata', 'Yudi Setiawan',
            'Nina Wulandari', 'Faisal Rahman', 'Eka Putri', 'Dimas Pratama', 'Ani Susilowati',
        ];

        $areas = ['Jakarta Selatan', 'Jakarta Utara', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Pusat', 'Tangerang', 'Bekasi', 'Depok', 'Bogor'];
        $streets = ['Jl. Sudirman', 'Jl. Thamrin', 'Jl. Gatot Subroto', 'Jl. Kuningan', 'Jl. TB Simatupang', 'Jl. Rasuna Said'];

        for ($i = 1; $i <= 100; $i++) {
            $name = $indonesianNames[array_rand($indonesianNames)] . ' ' . chr(65 + ($i % 26));
            $area = $areas[array_rand($areas)];
            $street = $streets[array_rand($streets)];
            
            $customer = Customer::create([
                'customer_code' => 'CUST-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'name' => $name,
                'email' => 'customer' . $i . '@example.com',
                'phone' => '08' . rand(1000000000, 9999999999),
                'address' => $street . ' No. ' . rand(1, 200) . ', ' . $area,
                'latitude' => -6.2 + (rand(-100, 100) / 1000),
                'longitude' => 106.8 + (rand(-100, 100) / 1000),
                'status' => $i % 10 == 0 ? 'suspended' : 'active',
                'created_by' => $admin->id,
            ]);

            $customerData[] = $customer;
        }

        // 3. Create Subscriptions (80% of customers have subscription)
        $this->command->info('📋 Creating subscriptions...');
        $allPackages = Package::all();
        
        foreach ($customerData as $index => $customer) {
            if ($index % 5 == 4) continue; // 20% tidak punya subscription

            $package = $allPackages->random();
            $startDate = Carbon::now()->subMonths(rand(1, 24));
            
            Subscription::create([
                'customer_id' => $customer->id,
                'package_id' => $package->id,
                'start_date' => $startDate,
                'status' => $customer->status == 'active' ? 'active' : 'suspended',
            ]);
        }

        // 4. Create Invoices (150 invoices - last 3 months)
        $this->command->info('🧾 Creating invoices...');
        $subscriptions = Subscription::with('customer', 'package')->get();
        $invoiceStatuses = ['draft', 'issued', 'pending', 'paid', 'overdue', 'void'];
        $invoiceCounter = 1;
        
        foreach ($subscriptions as $subscription) {
            // Create 1-3 invoices per subscription
            $invoiceCount = rand(1, 3);
            
            for ($i = 0; $i < $invoiceCount; $i++) {
                $issueDate = Carbon::now()->subMonths($invoiceCount - $i);
                $dueDate = $issueDate->copy()->addDays(7);
                
                $status = $invoiceStatuses[array_rand($invoiceStatuses)];
                if ($dueDate->isPast() && $status == 'issued') {
                    $status = 'overdue';
                }

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . $issueDate->format('Ym') . '-' . str_pad($invoiceCounter++, 5, '0', STR_PAD_LEFT),
                    'order_id' => 'ORD-' . $issueDate->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                    'customer_id' => $subscription->customer_id,
                    'subscription_id' => $subscription->id,
                    'invoice_date' => $issueDate,
                    'due_date' => $dueDate,
                    'status' => $status,
                    'subtotal' => $subscription->package->price,
                    'tax' => $subscription->package->price * 0.11,
                    'total' => $subscription->package->price * 1.11,
                    'paid_at' => $status == 'paid' ? $dueDate->subDays(rand(1, 7)) : null,
                    'created_by' => $admin->id,
                ]);

                // Create invoice item
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Langganan ' . $subscription->package->name . ' - ' . $issueDate->format('F Y'),
                    'quantity' => 1,
                    'unit_price' => $subscription->package->price,
                    'subtotal' => $subscription->package->price,
                ]);

                // Create payment if paid
                if ($status == 'paid') {
                    $paymentMethods = ['virtual_account', 'qris', 'bank_transfer', 'cash'];
                    Payment::create([
                        'payment_code' => 'PAY-' . $issueDate->format('Ymd') . '-' . rand(1000, 9999),
                        'invoice_id' => $invoice->id,
                        'customer_id' => $subscription->customer_id,
                        'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                        'amount' => $invoice->total,
                        'status' => 'settlement',
                        'settlement_time' => $invoice->paid_at,
                        'metadata' => [
                            'invoice_order_id' => $invoice->order_id,
                        ],
                    ]);
                }
            }
        }

        // 5. Create Support Tickets (50 tickets)
        $this->command->info('🎫 Creating support tickets...');
        $ticketSubjects = [
            'Internet lambat',
            'Koneksi terputus-putus',
            'Tidak bisa akses website tertentu',
            'Request ubah paket',
            'Billing issue',
            'Router mati',
            'Minta teknisi datang',
            'Gangguan total',
        ];

        $priorities = ['low', 'medium', 'high', 'urgent'];
        $ticketStatuses = ['open', 'in_progress', 'resolved', 'closed'];
        $ticketCounter = 1;

        foreach ($customerData as $index => $customer) {
            if ($index % 2 == 0) continue; // 50% customers punya ticket

            $ticketCount = rand(1, 2);
            for ($i = 0; $i < $ticketCount; $i++) {
                SupportTicket::create([
                    'ticket_number' => 'TKT-' . date('Ymd') . '-' . str_pad($ticketCounter++, 5, '0', STR_PAD_LEFT),
                    'customer_id' => $customer->id,
                    'subject' => $ticketSubjects[array_rand($ticketSubjects)],
                    'description' => 'Mohon bantuannya untuk masalah ini. Sudah berlangsung sejak kemarin.',
                    'priority' => $priorities[array_rand($priorities)],
                    'status' => $ticketStatuses[array_rand($ticketStatuses)],
                    'assigned_to' => rand(0, 1) ? $teknisi->id : null,
                    'created_by' => $admin->id,
                ]);
            }
        }

        // 6. Create Installations (30 installations)
        $this->command->info('🔧 Creating installations...');
        $installationStatuses = ['scheduled', 'in_progress', 'completed', 'cancelled'];
        $installCounter = 1;

        foreach ($customerData as $index => $customer) {
            if ($index % 3 != 0) continue; // 33% customers punya installation

            $package = $allPackages->random();
            $scheduledDate = Carbon::now()->addDays(rand(1, 30));

            Installation::create([
                'installation_number' => 'INST-' . date('Ymd') . '-' . str_pad($installCounter++, 5, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'package_id' => $package->id,
                'scheduled_date' => $scheduledDate,
                'status' => $installationStatuses[array_rand($installationStatuses)],
                'technician_id' => $teknisi->id,
                'installation_address' => $customer->address,
                'latitude' => $customer->latitude,
                'longitude' => $customer->longitude,
                'notes' => 'Instalasi baru',
            ]);
        }

        // 7. Create Giveaways (2 giveaways)
        $this->command->info('🎁 Creating giveaways...');
        $giveaway1 = Giveaway::create([
            'giveaway_code' => 'DISC50NEW',
            'name' => 'Diskon 50% untuk Pelanggan Baru',
            'description' => 'Dapatkan diskon 50% untuk 3 bulan pertama! Promo terbatas!',
            'type' => 'discount',
            'percentage' => 50,
            'quantity' => 100,
            'claimed' => 45,
            'valid_from' => Carbon::now()->subDays(10),
            'valid_until' => Carbon::now()->addDays(20),
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $giveaway2 = Giveaway::create([
            'giveaway_code' => 'FREEINSTALL',
            'name' => 'Gratis Instalasi - Promo Akhir Tahun',
            'description' => 'Gratis biaya instalasi untuk semua paket! Buruan daftar!',
            'type' => 'other',
            'quantity' => 50,
            'claimed' => 12,
            'valid_from' => Carbon::now()->subDays(5),
            'valid_until' => Carbon::now()->addDays(25),
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        // Create some claims
        for ($i = 0; $i < 20; $i++) {
            GiveawayClaim::create([
                'giveaway_id' => rand(0, 1) ? $giveaway1->id : $giveaway2->id,
                'customer_id' => $customerData[rand(0, 99)]->id,
                'claimed_at' => Carbon::now()->subDays(rand(1, 10)),
            ]);
        }

        // 8. Create CMS Pages
        $this->command->info('📄 Creating CMS pages...');
        CmsPage::create([
            'title' => 'Tentang Kami',
            'slug' => 'tentang-kami',
            'content' => '<h1>Tentang Agus Provider</h1><p>Kami adalah penyedia layanan internet terpercaya sejak 2020...</p>',
            'status' => 'published',
            'published_at' => Carbon::now()->subMonths(6),
        ]);

        CmsPage::create([
            'title' => 'Syarat dan Ketentuan',
            'slug' => 'syarat-ketentuan',
            'content' => '<h1>Syarat dan Ketentuan</h1><p>Dengan menggunakan layanan kami, Anda menyetujui...</p>',
            'status' => 'published',
            'published_at' => Carbon::now()->subMonths(6),
        ]);

        CmsPage::create([
            'title' => 'Kebijakan Privasi',
            'slug' => 'kebijakan-privasi',
            'content' => '<h1>Kebijakan Privasi</h1><p>Kami menghormati privasi Anda...</p>',
            'status' => 'published',
            'published_at' => Carbon::now()->subMonths(6),
        ]);

        $this->command->info('✅ Demo data seeding completed!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Packages: ' . Package::count());
        $this->command->info('   - Customers: ' . Customer::count());
        $this->command->info('   - Subscriptions: ' . Subscription::count());
        $this->command->info('   - Invoices: ' . Invoice::count());
        $this->command->info('   - Payments: ' . Payment::count());
        $this->command->info('   - Support Tickets: ' . SupportTicket::count());
        $this->command->info('   - Installations: ' . Installation::count());
        $this->command->info('   - Giveaways: ' . Giveaway::count());
    }
}
