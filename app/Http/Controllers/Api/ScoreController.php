<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Score;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    // Tüm skorları getir
    public function index()
    {
        $scores = Score::query()->orderBy('score', 'desc')->take(10)->get();
        return response()->json($scores);
    }

    // Belirli puan aralığındaki skorları getir
    public function indexByRange($range)
    {
        // Aralıkları ayır
        [$min, $max] = explode('-', $range);

        // Belirli aralıkta puanları çek
        $scores = Score::where('score', '>=', (int)$min)
            ->where('score', '<=', (int)$max)
            ->orderBy('score', 'desc')
            ->get();

        return response()->json($scores);
    }

    // Yeni skor ekle
    public function store(Request $request)
    {
        $filename = null;

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
