<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Soru;
use Illuminate\Http\Request;

class SorularController extends Controller
{
    public function index() {
        $products = Soru::get();

        return response()->json($products);
    }

    public function show($id) {
        $product = Soru::findOrFail($id);

        return response()->json($product);
    }

    public function store(Request $request) {
        $product = new Soru();

        $product->soru = $request->get('soru');
        $product->secenek_1 = $request->get('secenek_1');
        $product->secenek_2 = $request->get('secenek_2');
        $product->secenek_3 = $request->get('secenek_3');
        $product->secenek_4 = $request->get('secenek_4');

        $product->dogru_cevap = $request->get('dogru_cevap');
        $product->save();

        return response()->json($product);
    }

    public function update(Request $request, $id) {
        $product = Soru::findOrFail($id);

        $product->soru = $request->get('soru');
        $product->secenek_1 = $request->get('secenek_1');
        $product->secenek_2 = $request->get('secenek_2');
        $product->secenek_3 = $request->get('secenek_3');
        $product->secenek_4 = $request->get('secenek_4');

        $product->dogru_cevap = $request->get('dogru_cevap');
        $product->save();

        return response()->json($product);
    }

    public function destroy($id) {
        $product = Soru::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Silme işlemi başarılı']);
    }
}
