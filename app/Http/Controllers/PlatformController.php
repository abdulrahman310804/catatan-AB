<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlatformController extends Controller
{
    private function userId()
    {
        return DB::table('users')->where('email', 'abdul@demo.com')->value('id');
    }

    public function index()
    {
        $platforms = DB::table('platforms')
            ->where('user_id', $this->userId())
            ->orderBy('name')
            ->get();

        // Statistik per platform
        foreach ($platforms as $p) {
            $p->debts_count = DB::table('debts')->where('platform_id', $p->id)->count();
            $p->total_debt = DB::table('debts')->where('platform_id', $p->id)->sum('remaining_amount');
        }

        return view('platforms.index', compact('platforms'));
    }

    public function create()
    {
        return view('platforms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:paylater,bank,personal,ewallet,lainnya',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        DB::table('platforms')->insert([
            'user_id' => $this->userId(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'color' => $validated['color'] ?? '#3B82F6',
            'icon' => $validated['icon'] ?? 'fa-credit-card',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('platforms.index')->with('success', 'Platform berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $platform = DB::table('platforms')
            ->where('id', $id)
            ->where('user_id', $this->userId())
            ->first();

        if (!$platform) abort(404);

        return view('platforms.edit', compact('platform'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:paylater,bank,personal,ewallet,lainnya',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        DB::table('platforms')
            ->where('id', $id)
            ->where('user_id', $this->userId())
            ->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'color' => $validated['color'] ?? '#3B82F6',
                'icon' => $validated['icon'] ?? 'fa-credit-card',
                'updated_at' => now(),
            ]);

        return redirect()->route('platforms.index')->with('success', 'Platform berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Cek apakah platform masih dipakai hutang
        $dipakai = DB::table('debts')->where('platform_id', $id)->count();

        if ($dipakai > 0) {
            return redirect()->route('platforms.index')
                ->with('error', "Tidak bisa dihapus! Platform ini masih dipakai oleh {$dipakai} hutang.");
        }

        DB::table('platforms')
            ->where('id', $id)
            ->where('user_id', $this->userId())
            ->delete();

        return redirect()->route('platforms.index')->with('success', 'Platform berhasil dihapus!');
    }
}