<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatenController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
                function ($attribute, $value, $fail) {
                    $emails = array_values(array_filter(array_map('trim', explode(';', $value))));

                    if (count($emails) === 0) {
                        return $fail('Email wajib diisi.');
                    }

                    foreach ($emails as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            return $fail("Format email tidak valid: $email");
                        }
                    }
                }
            ],
        ]);
    }
}
