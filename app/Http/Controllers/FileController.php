<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // Dosyaları listelemek için index metodu
    public function index()
    {
        try {
            $files = File::all();

            if ($files->isEmpty()) {
                return response()->json([], 200);
            }

            return response()->json($files, 200);
        } catch (\Throwable $e) {
            \Log::error('Dosyalar alınırken hata oluştu: ' . $e->getMessage());
            return response()->json(['message' => 'Dosyalar alınırken bir hata oluştu.'], 500);
        }
    }

    // Yeni dosya yüklemek için store metodu
    public function store(Request $request)
    {
        try {
            // Yüklenen dosyaları doğrulama
            $request->validate([
                'file' => 'required|file', // Dosya zorunlu ve türü 'file' olmalı
                'name' => 'required|string',
                'category' => 'required|string|in:excel,word,powerpoint',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', // Görsel opsiyonel ve sadece belirli türlerde olmalı
                'uploader' => 'required|string', // 'uploader' alanını doğrulamaya ekliyoruz
                'uploaded_at' => 'required|date', // 'uploaded_at' alanını doğrulamaya ekliyoruz
            ]);

            // Dosya kaydetme

            if ($request->file('file')->isValid()) {
                $filePath = $request->file('file')->store('files', 'public');

                // Görsel kaydetme
                $imagePath = null;
                if ($request->hasFile('image') && $request->file('image')->isValid()) {
                    $imagePath = $request->file('image')->store('images', 'public');
                }

                // Yeni dosya kaydını veritabanına ekle
                $file = new File();
                $file->name = $request->name;
                $file->category = $request->category;
                $file->path = $filePath;
                $file->image_path = $imagePath; // Görsel yolu veritabanında tutuluyor
                $file->uploader = $request->uploader;
                $file->uploaded_at = $request->uploaded_at; // 'uploaded_at' alanını ekliyoruz
                $file->save();

                return response()->json(['message' => 'Dosya başarıyla yüklendi!', 'file' => $file], 201);
            } else {
                return response()->json(['message' => 'Dosya geçersiz.'], 422);
            }
        } catch (\Throwable $e) {
            // Hata durumunda log kaydı oluştur ve 500 hatası döndür
            \Log::error('Dosya yükleme hatası: ' . $e->getMessage());
            return response()->json(['message' => 'Dosya yüklenirken bir hata oluştu.'], 500);
        }
    }

    // Dosya indirme işlemi için download metodu
    public function download($id)
    {
        try {
            $file = File::findOrFail($id);

            // Dosya yolu ve adıyla birlikte indirilebilir şekilde döndür
            return Storage::disk('public')->download($file->path, $file->name);
        } catch (\Throwable $e) {
            \Log::error('Dosya indirme hatası: ' . $e->getMessage());
            return response()->json(['message' => 'Dosya indirilirken bir hata oluştu.'], 500);
        }
    }
}
