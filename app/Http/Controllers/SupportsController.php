<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportsController extends Controller
{
    public function users()
    {
        $users = \App\Support::all();
        return view('users', [
            'users' => $users
        ]);
    }
}
