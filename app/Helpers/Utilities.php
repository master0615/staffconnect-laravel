<?php
namespace App\Helpers;

use GuzzleHttp\Client;

class Utilities
{
    //change datetime to other timezone
    public static function applyTimezone($dt, $tz)
    {
        if ($tz && $tz != TZ) {
            $new = new DateTime($dt, new DateTimeZone($tz));
            $new->setTimeZone(new DateTimeZone(TZ));
            $new = date_format($new, 'Y-m-d H:i:s');
            $dt = $new;
        }
        return $dt;
    }

    /**
     * Calculates the great-circle distance between two points
     */
    public static function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "km") {
            return ($miles * 1.609344);
        } else {
            return $miles;
        }
    }

//geocode address for eg user
    public static function geocodeAddress($addr, $country = '')
    {
        $res = [];
        $results = self::googleGeocode($addr, $country);

        foreach ($results as $r) {
            $res['lat'] = $r->geometry->location->lat;
            $res['lon'] = $r->geometry->location->lng;
            $res['formatted_address'] = $r->formatted_address;
            return $res;
        }

        return 0;
    }

    //for user table filter
    public static function geoSearchForFilter($place, $radius, $country = '')
    {
        $locs = [];
        $results = self::googleGeocode($place, $country);

        foreach ($results as $r) {
            $loc = [];
            $lat = $r->geometry->location->lat;
            $lon = $r->geometry->location->lng;
            $place_id = $r->place_id;
            $loc['text'] = "Within $radius km of " . $r->formatted_address;
            $loc['id'] = "near:$lat:$lon:$radius:$place_id";
            $locs[] = $loc;
        }

        return $locs;
    }

    public static function getImageMeta($image)
    {
        @$exif = exif_read_data($image, 0, true);

        if ($exif) {
            $arr = ['lat' => null, 'lon' => null, 'created' => null];

            if (isset($exif['GPS'])) {
                if (isset($exif['GPS']['GPSLatitudeRef']) && isset($exif['GPS']['GPSLatitude']) && isset($exif['GPS']['GPSLongitudeRef']) && isset($exif['GPS']['GPSLongitude'])) {
                    $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
                    $GPSLatitude = $exif['GPS']['GPSLatitude'];
                    $GPSLongitudeRef = $exif['GPS']['GPSLongitudeRef'];
                    $GPSLongitude = $exif['GPS']['GPSLongitude'];

                    $lat_degrees = count($GPSLatitude) > 0 ? self::gps2Num($GPSLatitude[0]) : 0;
                    $lat_minutes = count($GPSLatitude) > 1 ? self::gps2Num($GPSLatitude[1]) : 0;
                    $lat_seconds = count($GPSLatitude) > 2 ? self::gps2Num($GPSLatitude[2]) : 0;

                    $lon_degrees = count($GPSLongitude) > 0 ? self::gps2Num($GPSLongitude[0]) : 0;
                    $lon_minutes = count($GPSLongitude) > 1 ? self::gps2Num($GPSLongitude[1]) : 0;
                    $lon_seconds = count($GPSLongitude) > 2 ? self::gps2Num($GPSLongitude[2]) : 0;

                    $lat_direction = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
                    $lon_direction = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;

                    $latitude = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60 * 60)));
                    $longitude = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60 * 60)));

                    $arr['lat'] = $latitude;
                    $arr['lon'] = $longitude;
                }
            }

            if (isset($exif['DateTimeOriginal'])) {
                $arr['created'] = $exif['DateTimeOriginal'];
            }

            return $arr;
        } else {
            return false;
        }
    }

    public static function googleGeocode($addr, $country = '')
    {
        $locs = [];
        $enc_addr = urlencode($addr);

        if ($country == '') {
            /* $q = "SELECT ccode FROM settings";
            $result = mysqli_query($conn, $q);
            $row = mysqli_fetch_row($result);
            $country = $row[0];

            if ($country == 'US') {
            $country = "US|CA";
            }*/
            //$country = 'AU';
        }

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$enc_addr&key=AIzaSyBM_EKHHhlQSwyhE1jTK4W8WFRLyY89uxI&components=country:$country";

        $client = new Client(); //GuzzleHttp\Client
        $result = $client->get($url);
        if ($result->getStatusCode() == '200') {
            $locs = json_decode($result->getBody());
            if (isset($locs->results)) {
                $locs = $locs->results;
            }
        }
        return $locs;
    }

    public static function gps2Num($coordPart)
    {
        $parts = explode('/', $coordPart);
        if (count($parts) <= 0) {
            return 0;
        }

        if (count($parts) == 1) {
            return $parts[0];
        }

        return floatval($parts[0]) / floatval($parts[1]);
    }

    //strpos but allow array needle
    public static function strposArr($haystack, $needle)
    {
        if (!is_array($needle)) {
            $needle = array($needle);
        }

        foreach ($needle as $what) {
            if (($pos = strpos($haystack, $what)) !== false) {
                return $pos;
            }

        }
        return false;
    }
}
