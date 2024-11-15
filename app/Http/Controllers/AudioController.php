<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $path = $file->store('public/audio_files');
            return response()->json(['file_path' => Storage::url($path)], 200);
        }
        return response()->json(['error' => 'Dosya yüklenemedi.'], 400);
    }
}


