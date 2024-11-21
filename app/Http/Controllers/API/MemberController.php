<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with('children')->get();
        return response()->json($members);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'image' => 'nullable|image|max:2048', // Maksimum 2MB
        ]);

        $data = $request->all();

        // Resim yükleme
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        // Hobileri JSON formatına çevir
        if (isset($data['hobbies'])) {
            $data['hobbies'] = json_decode($data['hobbies'], true);
        }

        $member = Member::create($data);

        return response()->json($member, 201);
    }

    public function show($id)
    {
        $member = Member::with('children')->findOrFail($id);
        return response()->json($member);
    }

    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        // Resim yükleme
        if ($request->hasFile('image')) {
            // Eski resmi sil
            if ($member->image) {
                Storage::disk('public')->delete($member->image);
            }
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        // Hobileri JSON formatına çevir
        if (isset($data['hobbies'])) {
            $data['hobbies'] = json_decode($data['hobbies'], true);
        }

        $member->update($data);

        return response()->json($member);
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        // Resmi sil
        if ($member->image) {
            Storage::disk('public')->delete($member->image);
        }

        $member->delete();

        return response()->json(null, 204);
    }
}
