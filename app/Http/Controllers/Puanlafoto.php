<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puanlafoto;

class PuanlafotoController extends Controller
{
    public function yukle(Request $request)
    {
        $request->validate([
            'ad' => 'required|string|max:255',
            'dosya' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Dosyayı kaydet
        $dosyaYolu = $request->file('dosya')->store('public/fotograflar');

        // Yeni fotoğraf kaydı oluştur
        Puanlafoto::create([
            'ad' => $request->ad,
            'dosya_yolu' => $dosyaYolu,
        ]);

        return response()->json(['message' => 'Fotoğraf başarıyla yüklendi.'], 201);
    }
}
