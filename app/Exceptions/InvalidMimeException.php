<?php

namespace App\Exceptions;

use Exception;

class InvalidMimeException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => 'The file type is not allowed or has an invalid MIME type.',
        ], 400);
    }
}
