<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\Storage;

class FamilyMemberController extends Controller
{
    /**
     * Tüm aile üyelerini listeleme
     */
    public function index()
    {
        return response()->json(FamilyMember::all(), 200);
    }

    /**
     * Yeni aile üyesi ekleme
     */
    public function store(Request $request)
    {
        // Verileri doğrula
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'relation' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Fotoğraf yükleme işlemi
        if ($request->hasFile('photo')) {
            $filePath = $request->file('photo')->store('public/files');
            $validated['photo_path'] = Storage::url($filePath);
        }

        // Aile üyesini oluştur
        $familyMember = FamilyMember::create($validated);

        return response()->json([
            'message' => 'Aile üyesi başarıyla eklendi.',
            'data' => $familyMember
        ], 201);
    }

    /**
     * Aile üyesini güncelleme
     */
    public function update(Request $request, $id)
    {
        // Aile üyesini bul
        $member = FamilyMember::findOrFail($id);

        // Verileri doğrula
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'relation' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Fotoğraf güncelleme işlemi
        if ($request->hasFile('photo')) {
            // Eski fotoğrafı sil
            if ($member->photo_path) {
                $oldPath = str_replace('/storage/', 'public/', $member->photo_path);
                Storage::delete($oldPath);
            }

            // Yeni fotoğrafı yükle
            $filePath = $request->file('photo')->store('public/files');
            $validated['photo_path'] = Storage::url($filePath);
        }

        // Aile üyesini güncelle
        $member->update($validated);

        return response()->json([
            'message' => 'Aile üyesi başarıyla güncellendi.',
            'data' => $member
        ], 200);
    }

    /**
     * Aile üyesini silme
     */
    public function destroy($id)
    {
        // Aile üyesini bul
        $member = FamilyMember::findOrFail($id);

        // Fotoğrafı sil
        if ($member->photo_path) {
            $oldPath = str_replace('/storage/', 'public/', $member->photo_path);
            Storage::delete($oldPath);
        }

        // Aile üyesini sil
        $member->delete();

        return response()->json([
            'message' => 'Aile üyesi başarıyla silindi.'
        ], 200);
    }
}
