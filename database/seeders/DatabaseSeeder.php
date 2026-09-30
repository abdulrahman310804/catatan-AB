<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. USER
        // ============================================
        DB::table('users')->updateOrInsert(
            ['email' => 'abdul@demo.com'],
            [
                'name' => 'Abdul',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $userId = DB::table('users')->where('email', 'abdul@demo.com')->value('id');

        // ============================================
        // 2. PLATFORMS
        // ============================================
        $platforms = [
            ['name' => 'Kredivo',         'type' => 'paylater', 'color' => '#EE4D2D', 'icon' => 'fa-credit-card'],
            ['name' => 'Shopee PayLater', 'type' => 'paylater', 'color' => '#F53D2D', 'icon' => 'fa-bag-shopping'],
            ['name' => 'BCA',             'type' => 'bank',     'color' => '#0066AE', 'icon' => 'fa-building-columns'],
            ['name' => 'Budi (Teman)',    'type' => 'personal', 'color' => '#7C3AED', 'icon' => 'fa-user'],
            ['name' => 'OVO',             'type' => 'ewallet',  'color' => '#4C3494', 'icon' => 'fa-wallet'],
        ];
        foreach ($platforms as $p) {
            DB::table('platforms')->updateOrInsert(
                ['user_id' => $userId, 'name' => $p['name']],
                $p + ['user_id' => $userId, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // ============================================
        // 3. WALLETS
        // ============================================
        $wallets = [
            ['name' => 'BCA Utama',  'type' => 'bank',    'balance' => 1500000, 'color' => '#0066AE'],
            ['name' => 'Dana',       'type' => 'ewallet', 'balance' => 250000,  'color' => '#118EEA'],
            ['name' => 'OVO',        'type' => 'ewallet', 'balance' => 100000,  'color' => '#4C3494'],
            ['name' => 'Uang Tunai', 'type' => 'cash',    'balance' => 500000,  'color' => '#10B981'],
        ];
        foreach ($wallets as $w) {
            DB::table('wallets')->updateOrInsert(
                ['user_id' => $userId, 'name' => $w['name']],
                $w + ['user_id' => $userId, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // ============================================
        // 4. DEBTS
        // ============================================
        $kredivo = DB::table('platforms')->where('user_id', $userId)->where('name', 'Kredivo')->value('id');
        $bca     = DB::table('platforms')->where('user_id', $userId)->where('name', 'BCA')->value('id');
        $budi    = DB::table('platforms')->where('user_id', $userId)->where('name', 'Budi (Teman)')->value('id');

        $debts = [
            [
                'name' => 'Hutang HP Samsung', 'platform_id' => $kredivo,
                'total_amount' => 2000000, 'paid_amount' => 600000, 'remaining_amount' => 1400000,
                'start_date' => now()->subDays(30)->toDateString(),
                'due_date'   => now()->addDays(3)->toDateString(),
                'daily_target' => 466666, 'status' => 'active',
                'notes' => 'Cicilan HP Samsung Galaxy A54',
            ],
            [
                'name' => 'Pinjaman BCA', 'platform_id' => $bca,
                'total_amount' => 3000000, 'paid_amount' => 600000, 'remaining_amount' => 2400000,
                'start_date' => now()->subDays(15)->toDateString(),
                'due_date'   => now()->addDays(20)->toDateString(),
                'daily_target' => 120000, 'status' => 'active',
                'notes' => 'Kredit tanpa agunan',
            ],
            [
                'name' => 'Pinjam Budi', 'platform_id' => $budi,
                'total_amount' => 1700000, 'paid_amount' => 0, 'remaining_amount' => 1700000,
                'start_date' => now()->subDays(5)->toDateString(),
                'due_date'   => now()->addDays(60)->toDateString(),
                'daily_target' => 28333, 'status' => 'active',
                'notes' => 'Pinjam buat modal jualan online',
            ],
        ];
        foreach ($debts as $d) {
            DB::table('debts')->updateOrInsert(
                ['user_id' => $userId, 'name' => $d['name']],
                $d + ['user_id' => $userId, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // ============================================
        // 5. DEBT PAYMENTS
        // ============================================
        $bcaWallet = DB::table('wallets')->where('user_id', $userId)->where('name', 'BCA Utama')->value('id');
        $hp        = DB::table('debts')->where('user_id', $userId)->where('name', 'Hutang HP Samsung')->value('id');
        $pinjaman  = DB::table('debts')->where('user_id', $userId)->where('name', 'Pinjaman BCA')->value('id');

        $payments = [
            ['debt_id' => $hp,       'amount' => 300000, 'payment_date' => now()->subDays(20)->toDateString(), 'notes' => 'Cicilan pertama'],
            ['debt_id' => $hp,       'amount' => 300000, 'payment_date' => now()->subDays(10)->toDateString(), 'notes' => 'Cicilan kedua'],
            ['debt_id' => $pinjaman, 'amount' => 600000, 'payment_date' => now()->subDays(5)->toDateString(),  'notes' => 'Angsuran bulan 1'],
        ];
        foreach ($payments as $p) {
            if (!$p['debt_id']) continue;
            DB::table('debt_payments')->updateOrInsert(
                ['debt_id' => $p['debt_id'], 'user_id' => $userId, 'payment_date' => $p['payment_date']],
                [
                    'wallet_id' => $bcaWallet,
                    'amount' => $p['amount'],
                    'method' => 'transfer',
                    'notes' => $p['notes'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ============================================
        // 6. SAVING GOAL
        // ============================================
        DB::table('saving_goals')->updateOrInsert(
            ['user_id' => $userId, 'name' => 'Target Lunasin Kredivo Bulan Ini'],
            [
                'target_amount' => 1500000,
                'collected_amount' => 750000,
                'daily_target' => 50000,
                'period_start' => now()->startOfMonth()->toDateString(),
                'period_end' => now()->endOfMonth()->toDateString(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // ============================================
        // 7. SAVINGS
        // ============================================
        $danaWallet = DB::table('wallets')->where('user_id', $userId)->where('name', 'Dana')->value('id');
        $goalId     = DB::table('saving_goals')->where('user_id', $userId)->value('id');

        $savings = [
            ['amount' => 50000,  'saving_date' => now()->subDays(5)->toDateString(), 'source' => 'Freelance'],
            ['amount' => 100000, 'saving_date' => now()->subDays(3)->toDateString(), 'source' => 'Gaji'],
            ['amount' => 250000, 'saving_date' => now()->subDays(1)->toDateString(), 'source' => 'Jualan Online'],
            ['amount' => 100000, 'saving_date' => now()->toDateString(),             'source' => 'Gaji'],
        ];
        foreach ($savings as $s) {
            DB::table('savings')->updateOrInsert(
                ['user_id' => $userId, 'saving_date' => $s['saving_date'], 'amount' => $s['amount']],
                [
                    'wallet_id' => $danaWallet,
                    'saving_goal_id' => $goalId,
                    'source' => $s['source'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ============================================
        // 8. REMINDERS
        // ============================================
        if ($hp) {
            DB::table('reminders')->updateOrInsert(
                ['user_id' => $userId, 'debt_id' => $hp, 'type' => 'h-1'],
                [
                    'title' => 'Hutang Kredivo jatuh tempo besok!',
                    'message' => 'Segera bayar Rp 1.400.000 untuk menghindari denda.',
                    'remind_at' => now()->addDay()->setTime(8, 0),
                    'is_sent' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('✅ Seeder selesai! Semua data dummy berhasil dimasukkan.');
    }
}