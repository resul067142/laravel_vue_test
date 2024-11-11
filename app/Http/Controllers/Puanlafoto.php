<?php

// app/Http/Controllers/PuanlafotoController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puanlafoto;

class PuanlafotoController extends Controller
{
    public function index()
    {
        $fotos = Puanlafoto::orderBy('ortalama_puan', 'desc')->get();
        return response()->json($fotos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'dosya_yolu' => 'required|string',
            'guzellik' => 'required|integer|min:0|max:10',
            'yetenek' => 'required|integer|min:0|max:10',
            'kisisel' => 'required|integer|min:0|max:10',
        ]);

        $ortalama_puan = ($validated['guzellik'] + $validated['yetenek'] + $validated['kisisel']) / 3;
        $validated['ortalama_puan'] = round($ortalama_puan, 2);

        $foto = Puanlafoto::create($validated);

        return response()->json($foto, 201);
    }

    public function yukle(Request $request)
    {
        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'dosya' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $dosyaAdi = time() . '.' . $request->dosya->extension();
        $request->dosya->move(public_path('images'), $dosyaAdi);

        $foto = Puanlafoto::create([
            'ad' => $validated['ad'],
            'dosya_yolu' => '/images/' . $dosyaAdi,
        ]);

        return response()->json($foto, 201);
    }
}
