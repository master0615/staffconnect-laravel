<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * POST /auth/login
     */
    protected function login(Request $request)
    {
        $request->validate([
            'client_id' => "required|in:2,3",
        ]);

        $client = app(\Hyn\Tenancy\Database\Connection::class)->get()
            ->table('oauth_clients')
            ->where('id', $request->client_id)
            ->first();

        $request->request->add([
            'username' => $request->username,
            'password' => $request->password,
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'scope' => '*',
        ]);

        $proxy = Request::create('oauth/token', 'POST');
        $response = Route::dispatch($proxy);

        if (!$response->isSuccessful()) {
            throw new \App\Exceptions\InvalidCredentialsException();
        }

        $data = json_decode($response->getContent());
        $data->user = \App\User::select('id', 'fname', 'lname', 'lvl', 'ppic_a', 'active')->where('email', $request->username)->firstOrFail();

        if ($data->user->active == 'inactive') {
            throw new \App\Exceptions\InactiveUserException();
        }

        if ($data->user->active == 'bl') {
            // TODO some agencies still want them to be able to login anyway do they don't call and complain. for fucks sake
            throw new BlacklistedUserException();
        }

        return response()->api($data);
    }

    /**
     * POST /loginAs/{id}
     */
    public function loginAs(Request $request)
    {
        $request->validate([
            'user_id' => "required|numeric|notin:" . Auth::id() . "|exists:tenant.users,id",
        ]);
        $u = \App\User::select('id', 'fname', 'lname', 'lvl', 'ppic_a', 'active')->findOrFail($request->user_id);

        //check if allowed
        $allowed = 0;
        if (Auth::user()->lvl == 'owner') {
            if ($u->lvl == 'admin' || $u->lvl == 'staff') {
                $allowed = 1;
            }
        } elseif (Auth::user()->lvl == 'admin') {
            if ($u->lvl == 'staff') {
                $allowed = 1;
            }
        }

        if ($allowed) {
            $oat = Auth::user()->oauthAccessTokens()->where([['revoked', '0'], ['client_id', '2']])->first();
            $oat->logged_in_as_id = $u->id;
            $oat->save();

        } else {
            throw new \App\Exceptions\NotAllowedException();
        }

        return response()->api([
            'message' => "Logged in as " . $u->fname . ' ' . $u->lname,
            'user' => $u,
        ]);
    }

    /**
     * POST /auth/refresh
     */
    protected function refreshToken(Request $request)
    {
        $request->validate([
            'client_id' => "required|in:2,3",
        ]);

        $client = app(\Hyn\Tenancy\Database\Connection::class)->get()
            ->table('oauth_clients')
            ->where('id', $request->client_id)
            ->first();

        $request->request->add([
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $client->id,
            'client_secret' => $client->secret,
        ]);

        $proxy = Request::create('/oauth/token', 'POST');

        return Route::dispatch($proxy);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('api');
    }

    /**
     * POST /auth/logout
     */
    public function logout(Request $request)
    {
        if (!$this->guard()->check()) {
            return response()->api([
                'message' => "No active user session was found.",
            ], 404);
        }

        // Taken from: https://laracasts.com/discuss/channels/laravel/laravel-53-passport-password-grant-logout
        $request->user('api')
            ->token()
            ->revoke();

        Auth::guard()->logout();

        Session::flush();

        Session::regenerate();

        return response()->api([
            'message' => "Bye!",
        ]);
    }

    /**
     * POST /logoutAs/{id}
     */
    public function logoutAs()
    {
        $u = \App\User::select('id', 'fname', 'lname', 'lvl', 'ppic_a', 'active')->findOrFail(Auth::id());

        $oat = Auth::user()->oauthAccessTokens()->where([['revoked', '0'], ['client_id', '2']])->first();
        $oat->logged_in_as_id = null;
        $oat->save();

        return response()->api([
            'message' => "Logged in as " . $u->fname . ' ' . $u->lname,
            'user' => $u,
        ]);
    }
}
