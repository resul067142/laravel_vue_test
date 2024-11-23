<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // Üyelerin listesini döndürür
    public function index()
    {
        $members = Member::all();
        return response()->json($members);
    }

    // Yeni bir üye oluşturur
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'role' => 'required|string',
            'image' => 'required|string',
            'details' => 'nullable|string',
            'birthDate' => 'nullable|string',
            'occupation' => 'nullable|string',
            'hobbies' => 'nullable|array',
            'contact' => 'nullable|string',
            'socialMedia' => 'nullable|string',
            'generation' => 'required|string',
        ]);

        $member = Member::create($validatedData);
        return response()->json($member, 201);
    }

    // Belirli bir üyeyi döndürür
    public function show($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Üye bulunamadı'], 404);
        }
        return response()->json($member);
    }

    // Bir üyeyi günceller
    public function update(Request $request, $id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Üye bulunamadı'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string',
            'role' => 'sometimes|required|string',
            'image' => 'sometimes|required|string',
            'details' => 'nullable|string',
            'birthDate' => 'nullable|string',
            'occupation' => 'nullable|string',
            'hobbies' => 'nullable|array',
            'contact' => 'nullable|string',
            'socialMedia' => 'nullable|string',
            'generation' => 'sometimes|required|string',
        ]);

        $member->update($validatedData);
        return response()->json($member);
    }

    // Bir üyeyi siler
    public function destroy($id)
    {
        $member = Member::find($id);
        if (!$member) {
            return response()->json(['message' => 'Üye bulunamadı'], 404);
        }
        $member->delete();
        return response()->json(['message' => 'Üye silindi']);
    }
}
