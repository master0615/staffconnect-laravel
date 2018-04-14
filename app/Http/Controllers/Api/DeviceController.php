<?php
namespace App\Http\Controllers\Api;

use App\Device;
use Illuminate\Http\Request;

/**
 *  @SWG\Tag(
 *      name="user",
 *      description="User"
 *  )
 */
class DeviceController extends Controller
{

    public function __construct()
    {
        // $this->middleware( 'cors' );
    }

    /**
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function registerDevice(Request $request)
    {
        $request->validate([
            'firebase_token' => 'required',
            'user_id' => 'required'
        ], [
            'firebase_token.required' => "The firebase token is required",
            'firebase_token.unique' => "The firebase token is already registered on the system",
            'user_id.required' => 'User ID is required'
        ]);

        try {
            $device = new Device();
            $device->user_id = $request->user_id;
            $device->firebase_token = $request->firebase_token;
            $device->save();

            return response()->api([
                'id' => $device->id
            ]);
        } catch (\Exception $e){
            return response()->api([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * @param  int $deviceId
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function updateDevice($deviceId, Request $request) {
        $request->validate([
            'firebase_token' => 'required',
            'user_id' => 'required'
        ], [
            'firebase_token.required' => "The firebase token is required",
            'firebase_token.unique' => "The firebase token is already registered on the system",
            'user_id.required' => 'User ID is required'
        ]);

        try {           
            $device = Device::findOrFail($deviceId);
            $device->firebase_token = $request->firebase_token;
            $device->user_id = $request->user_id;
            $device->save();

            return response()->api([
                'message' => "device token updated"
            ]);
        } catch (\Exception $e){
            return response()->api([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * @param  int $deviceId
     * @return \Illuminate\Http\Response
     */
    public function removeDevice($deviceId) {
        try {           
            $device = Device::findOrFail($deviceId);
            $device->delete();

            return response()->api([
                'message' => "device removed"
            ]);
        } catch (\Exception $e){
            return response()->api([
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
