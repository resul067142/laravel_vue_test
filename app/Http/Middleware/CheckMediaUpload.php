<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMediaUpload
{
    public function handle(Request $request, Closure $next)
    {
        // Örneğin, dosya sayısını kontrol edebilirsiniz
        if (!$request->hasFile('files')) {
            return response()->json(['error' => 'Dosya yüklenmedi.'], 400);
        }

        // Diğer kontroller...

        return $next($request);
    }
}
