<?php

// app/Http/Kernel.php
namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'adminapproval' => \App\Http\Middleware\AdminApprovalMiddleware::class,
        'no-cache' => \App\Http\Middleware\NoCacheMiddleware::class,
        // Daftarkan middleware lainnya
    ];
}
