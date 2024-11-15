<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * CSRF korumasından muaf tutulacak URI'lar
     */
    protected $except = [
        'api/*', // API rotalarını muaf tut
    ];
}
