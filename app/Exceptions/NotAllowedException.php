<?php

namespace App\Exceptions;

use Exception;

class NotAllowedException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => 'You are not allowed to do this.',
        ], 401);
    }
}
