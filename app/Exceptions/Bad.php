<?php

namespace App\Exceptions;

use Exception;

class Bad extends Exception
{
    protected $msg, $code;

    public function __construct($code = 400, $msg = 'Bad Request.')
    {
        $this->msg = $msg;
        $this->code = $code;
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->msg,
        ], $this->code);
    }
}
