<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    private function userId()
    {
        return DB::table('users')->where('email', 'abdul@demo.com')->value('id');
    }

    public function index(Request $request)
    {
        $userId = $this->userId();
        $filter = $request->get('filter', 'semua'); // semua | aktif | lunas | overdue

        $query = DB::table('debts')
            ->leftJoin('platforms', 'debts.platform_id', '=', 'platforms.id')
            ->where('debts.user_id', $userId)
            ->select(
                'debts.*',
                'platforms.name as platform_name',
                'platforms.color as platform_color',
                'platforms.icon as platform_icon'
            );

        if ($filter === 'aktif') {
            $query->where('debts.status', 'active');
        } elseif ($filter === 'lunas') {
            $query->where('debts.status', 'paid');
        } elseif ($filter === 'overdue') {
            $query->where('debts.status', 'active')->where('debts.due_date', '<', now()->toDateString());
        }

        $debts = $query->orderBy('debts.due_date', 'asc')->get();

        // Statistik
        $stats = [
            'total' => DB::table('debts')->where('user_id', $userId)->sum('total_amount'),
            'sisa' => DB::table('debts')->where('user_id', $userId)->where('status', 'active')->sum('remaining_amount'),
            'terbayar' => DB::table('debts')->where('user_id', $userId)->sum('paid_amount'),
            'jumlah_aktif' => DB::table('debts')->where('user_id', $userId)->where('status', 'active')->count(),
        ];

        return view('debts.index', compact('debts', 'filter', 'stats'));
    }

    public function create()
    {
        $platforms = DB::table('platforms')->where('user_id', $this->userId())->orderBy('name')->get();
        return view('debts.create', compact('platforms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'platform_id' => 'required|exists:platforms,id',
            'total_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        // Hitung target harian otomatis
        $dueDate = Carbon::parse($validated['due_date']);
        $hariTersisa = max(now()->startOfDay()->diffInDays($dueDate->startOfDay(), false), 1);
        $dailyTarget = round($validated['total_amount'] / $hariTersisa);

        DB::table('debts')->insert([
            'user_id' => $this->userId(),
            'platform_id' => $validated['platform_id'],
            'name' => $validated['name'],
            'total_amount' => $validated['total_amount'],
            'paid_amount' => 0,
            'remaining_amount' => $validated['total_amount'],
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
            'daily_target' => $dailyTarget,
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('debts.index')->with('success', 'Hutang berhasil ditambahkan!');
    }

    public function show($id)
    {
        $userId = $this->userId();
        $debt = DB::table('debts')
            ->leftJoin('platforms', 'debts.platform_id', '=', 'platforms.id')
            ->where('debts.id', $id)
            ->where('debts.user_id', $userId)
            ->select('debts.*', 'platforms.name as platform_name', 'platforms.color as platform_color', 'platforms.icon as platform_icon')
            ->first();

        if (!$debt) abort(404);

        // Hitung info tambahan
        $debt->days_left = now()->startOfDay()->diffInDays(Carbon::parse($debt->due_date)->startOfDay(), false);
        $debt->progress_percent = $debt->total_amount > 0 ? round(($debt->paid_amount / $debt->total_amount) * 100) : 0;

        // Riwayat pembayaran
        $payments = DB::table('debt_payments')
            ->leftJoin('wallets', 'debt_payments.wallet_id', '=', 'wallets.id')
            ->where('debt_payments.debt_id', $id)
            ->orderBy('payment_date', 'desc')
            ->select('debt_payments.*', 'wallets.name as wallet_name')
            ->get();

        $wallets = DB::table('wallets')->where('user_id', $userId)->orderBy('name')->get();

        return view('debts.show', compact('debt', 'payments', 'wallets'));
    }

    public function edit($id)
    {
        $platforms = DB::table('platforms')->where('user_id', $this->userId())->orderBy('name')->get();
        $debt = DB::table('debts')->where('id', $id)->where('user_id', $this->userId())->first();

        if (!$debt) abort(404);

        return view('debts.edit', compact('debt', 'platforms'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'platform_id' => 'required|exists:platforms,id',
            'total_amount' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $userId = $this->userId();
        $debt = DB::table('debts')->where('id', $id)->where('user_id', $userId)->first();
        if (!$debt) abort(404);

        // Hitung ulang
        $remaining = $validated['total_amount'] - $debt->paid_amount;
        if ($remaining < 0) $remaining = 0;

        $dueDate = Carbon::parse($validated['due_date']);
        $hariTersisa = max(now()->startOfDay()->diffInDays($dueDate->startOfDay(), false), 1);
        $dailyTarget = $remaining > 0 ? round($remaining / $hariTersisa) : 0;

        DB::table('debts')->where('id', $id)->update([
            'platform_id' => $validated['platform_id'],
            'name' => $validated['name'],
            'total_amount' => $validated['total_amount'],
            'remaining_amount' => $remaining,
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
            'daily_target' => $dailyTarget,
            'status' => $remaining <= 0 ? 'paid' : 'active',
            'notes' => $validated['notes'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('debts.show', $id)->with('success', 'Hutang berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::table('debts')->where('id', $id)->where('user_id', $this->userId())->delete();
        return redirect()->route('debts.index')->with('success', 'Hutang berhasil dihapus!');
    }

    // ==========================================
    // BAYAR HUTANG
    // ==========================================
    public function pay(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'wallet_id' => 'nullable|exists:wallets,id',
            'method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $userId = $this->userId();
        $debt = DB::table('debts')->where('id', $id)->where('user_id', $userId)->first();
        if (!$debt) abort(404);

        // Cek jumlah bayar tidak melebihi sisa
        if ($validated['amount'] > $debt->remaining_amount) {
            return back()->with('error', 'Jumlah bayar melebihi sisa hutang! Sisa: Rp ' . number_format($debt->remaining_amount, 0, ',', '.'));
        }

        DB::beginTransaction();
        try {
            // 1. Catat pembayaran
            DB::table('debt_payments')->insert([
                'debt_id' => $id,
                'user_id' => $userId,
                'wallet_id' => $validated['wallet_id'] ?? null,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'method' => $validated['method'] ?? 'transfer',
                'notes' => $validated['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Update total paid & remaining
            $newPaid = $debt->paid_amount + $validated['amount'];
            $newRemaining = $debt->total_amount - $newPaid;
            $newStatus = $newRemaining <= 0 ? 'paid' : 'active';

            // Hitung ulang target harian
            $dueDate = Carbon::parse($debt->due_date);
            $hariTersisa = max(now()->startOfDay()->diffInDays($dueDate->startOfDay(), false), 1);
            $newDailyTarget = $newRemaining > 0 ? round($newRemaining / $hariTersisa) : 0;

            DB::table('debts')->where('id', $id)->update([
                'paid_amount' => $newPaid,
                'remaining_amount' => $newRemaining,
                'daily_target' => $newDailyTarget,
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

            // 3. Update saldo wallet (kalau pilih wallet)
            if (!empty($validated['wallet_id'])) {
                DB::table('wallets')->where('id', $validated['wallet_id'])->decrement('balance', $validated['amount']);
            }

            DB::commit();
            return back()->with('success', 'Pembayaran berhasil dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
        }
    }
}