<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Score;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function index()
    {
        $scores = Score::query()->orderBy('score', 'desc')->take(10)->get();
        return response()->json($scores);
    }

    public function store(Request $request) {

        if ($request->file('photo')) {
            $filename = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('uploads'), $filename);
        }

        $score = Score::query()->create([
            'name' => $request->input('name'),
            'score' => $request->input('score'),
            'img_url' => $filename
        ]);

        return response()->json($score);
    }


}
