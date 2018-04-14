<?php

namespace App\Http\Middleware;

use Closure;

class CORS {
	public function handle( $request, Closure $next ) {
		return $next( $request);
	}
}
