<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies;

class ExceptWpCookies extends EncryptCookies
{
    protected $except = [
        'thevoice_sess',
    ];
}
