<?php

namespace App\Exceptions;

use Exception;

class UploadFileException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => 'Please upload a file.',
        ], 400);
    }
}
