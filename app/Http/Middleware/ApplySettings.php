<?php
namespace App\Http\Middleware;

use App\Setting;
use Closure;

class ApplySettings
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // timezone
        $tz = Setting::find(1)->value;
        define('TZ', $tz);
        date_default_timezone_set(TZ);

        // country & locatlisation
        $ccode = Setting::find(2)->value;
        define('CCODE', $ccode);

        // short date format
        $dformat = Setting::find(3)->value;
        define('DFORMAT', $dformat);

        // time format
        $tformat = Setting::find(4)->value;
        define('TFORMAT', $tformat);

        // phone code
        $phoneCode = Setting::find(5)->value;
        define('PHONE_CODE', $phoneCode);

        // currency
        $curs = Setting::find(6)->value;
        define('CURS', $curs);

        // distance
        $dists = Setting::find(7)->value;
        define('DISTS', $dists);
        $distl = Setting::find(8)->value;
        define('DISTL', $distl);

        return $next($request);
    }
}
