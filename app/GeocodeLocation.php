<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class GeocodeLocation extends Model
{
/**
 * @SWG\Property(
 * property="id",
 * type="integer",
 * description="Primary key id"
 * )
 * @SWG\Property(
 * property="lat",
 * type="double",
 * description="Latitude"
 * )
 * @SWG\Property(
 * property="lon",
 * type="double",
 * description="Longitude"
 * )
 * @SWG\Property(
 * property="formatted_address",
 * type="string",
 * maximum="100",
 * minimum="1",
 * description="Formatted address"
 * )
 * @SWG\Property(
 * property="ca_short",
 * type="string",
 * maximum="30",
 * minimum="1",
 * description="Locality short name"
 * )
 * @SWG\Property(
 * property="ca_long",
 * type="string",
 * maximum="50",
 * minimum="1",
 * description="Locality long name"
 * )
 * @SWG\Property(
 * property="aa1_short",
 * type="string",
 * maximum="30",
 * minimum="1",
 * description="Administrative area 1 short name"
 * )
 * @SWG\Property(
 * property="aa1_long",
 * type="string",
 * maximum="50",
 * minimum="1",
 * description="Administrative area 1 long name"
 * )
 * @SWG\Property(
 * property="aa2_short",
 * type="string",
 * maximum="30",
 * minimum="1",
 * description="Administrative area 2 short name"
 * )
 * @SWG\Property(
 * property="aa2_long",
 * type="string",
 * maximum="50",
 * minimum="1",
 * description="Administrative area 2 long name"
 * )
 * @SWG\Property(
 * property="aa3_short",
 * type="string",
 * maximum="30",
 * minimum="1",
 * description="Administrative area 3 short name"
 * )
 * @SWG\Property(
 * property="aa3_long",
 * type="string",
 * maximum="50",
 * minimum="1",
 * description="Administrative area 3 long name"
 * )
 * @SWG\Property(
 * property="country_short",
 * type="string",
 * maximum="2",
 * minimum="1",
 * description="2 letter country code"
 * )
 * @SWG\Property(
 * property="country_long",
 * type="string",
 * maximum="30",
 * minimum="1",
 * description="Country name"
 * )
 * @SWG\Property(
 * property="postal_code",
 * type="string",
 * maximum="10",
 * minimum="1",
 * description="Postal code"
 * )
 */
}
