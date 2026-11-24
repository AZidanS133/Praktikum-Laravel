<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KalkulatorController extends Controller
{
    public function segitiga(Request $request)
    {
        $alas   = $request->query('alas');
        $tinggi = $request->query('tinggi');
        $luas   = null;

        if ($alas !== null && $tinggi !== null) {
            $request->validate([
                'alas'   => 'required|numeric|min:0',
                'tinggi' => 'required|numeric|min:0',
            ]);
            $luas = 0.5 * $alas * $tinggi;
        }

        return view('segitiga', compact('alas', 'tinggi', 'luas'));
    }
}