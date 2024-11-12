<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\Storage;

class FamilyMemberController extends Controller
{
    // Yeni aile üyesi ekleme
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'relation' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $filePath = $request->file('photo')->store('public/files');
            $validated['photo_path'] = str_replace('public/', 'storage/', $filePath);
        }

        FamilyMember::create($validated);

        return response()->json(['message' => 'Aile üyesi başarıyla eklendi'], 201);
    }

    // Aile üyesini güncelleme
    public function update(Request $request, $id)
    {
        $member = FamilyMember::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'relation' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($member->photo_path) {
                Storage::delete(str_replace('storage/', 'public/', $member->photo_path));
            }
            $filePath = $request->file('photo')->store('public/files');
            $validated['photo_path'] = str_replace('public/', 'storage/', $filePath);
        }

        $member->update($validated);

        return response()->json(['message' => 'Aile üyesi başarıyla güncellendi'], 200);
    }

    // Aile üyesini silme
    public function destroy($id)
    {
        $member = FamilyMember::findOrFail($id);
        if ($member->photo_path) {
            Storage::delete(str_replace('storage/', 'public/', $member->photo_path));
        }
        $member->delete();

        return response()->json(['message' => 'Aile üyesi başarıyla silindi'], 200);
    }

    // Aile üyelerini listeleme
    public function index()
    {
        return FamilyMember::all();
    }
}
