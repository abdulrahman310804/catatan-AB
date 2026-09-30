<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Sementara: ambil user dari seeder (Abdul)
        // Nanti kalau sudah pakai login, ganti jadi Auth::id()
        $userId = DB::table('users')->where('email', 'abdul@demo.com')->value('id');

        if (!$userId) {
            return view('dashboard', ['noData' => true]);
        }

        // ============ STATISTIK ============
        $totalHutang = DB::table('debts')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->sum('remaining_amount');

        $totalPaid = DB::table('debts')
            ->where('user_id', $userId)
            ->sum('paid_amount');

        $totalOriginal = DB::table('debts')
            ->where('user_id', $userId)
            ->sum('total_amount');

        $targetHarian = DB::table('debts')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->sum('daily_target');

        $tabungan = DB::table('savings')
            ->where('user_id', $userId)
            ->sum('amount');

        $jumlahHutangAktif = DB::table('debts')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->count();

        $progressPersen = $totalOriginal > 0
            ? round(($totalPaid / $totalOriginal) * 100)
            : 0;

        $tabunganPersen = $totalHutang > 0
            ? round(($tabungan / $totalHutang) * 100)
            : 0;

        // ============ DAFTAR HUTANG AKTIF ============
        $debts = DB::table('debts')
            ->leftJoin('platforms', 'debts.platform_id', '=', 'platforms.id')
            ->where('debts.user_id', $userId)
            ->where('debts.status', 'active')
            ->select(
                'debts.*',
                'platforms.name as platform_name',
                'platforms.color as platform_color',
                'platforms.icon as platform_icon'
            )
            ->orderBy('debts.due_date', 'asc')
            ->get();

        // Hitung hari tersisa & progress per hutang
        foreach ($debts as $debt) {
            $debt->days_left = now()->startOfDay()->diffInDays(
                \Carbon\Carbon::parse($debt->due_date)->startOfDay(),
                false
            );
            $debt->progress_percent = $debt->total_amount > 0
                ? round(($debt->paid_amount / $debt->total_amount) * 100)
                : 0;
        }

        // ============ PENGINGAT ============
        $reminders = DB::table('reminders')
            ->where('user_id', $userId)
            ->where('is_sent', false)
            ->orderBy('remind_at', 'asc')
            ->limit(5)
            ->get();

        // ============ USER ============
        $user = DB::table('users')->where('id', $userId)->first();

        return view('dashboard', compact(
            'user',
            'totalHutang',
            'totalPaid',
            'totalOriginal',
            'targetHarian',
            'tabungan',
            'tabunganPersen',
            'jumlahHutangAktif',
            'progressPersen',
            'debts',
            'reminders'
        ));
    }
}