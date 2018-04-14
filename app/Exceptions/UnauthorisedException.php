<?php

namespace App\Exceptions;

use Exception;

class UnauthorisedException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => 'You are not authorised to do this.',
        ], 401);
    }
}
