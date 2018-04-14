<?php

namespace App\Exceptions;

use Exception;

class BlacklistedUserException extends Exception
{
    public function render($request) {
		return response()->json([
			'message' => 'Your account is inactive. Please contact us to re-activate it.',
		], 401);
	}
}
