<?php

namespace App\Exceptions;

use Exception;

class SystemProtected extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => 'You cannot do this.',
        ], 403);
    }
}
