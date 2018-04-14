<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function render($request) {
		return response()->json([
			'message' => 'The email address or password is incorrect.',
		], 401);
	}
}
