<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // DUMMY: ambil nama dari input username (biar "Sehun" dinamis)
        // Nanti kalau sudah DB, ganti dari database.
        $username = trim($request->input('username', 'Admin'));
        $displayName = $username !== '' ? $username : 'Admin';

        session([
            'admin_logged_in' => true,
            'admin_name' => $displayName,
        ]);

        // ✅ login sukses langsung ke dashboard
        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_logged_in', 'admin_name']);
        return redirect()->route('admin.login.form');
    }
}
