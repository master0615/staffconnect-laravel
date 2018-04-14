<?php

namespace App\Http\Controllers\Api;

use App\ProfileVideo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileVideoController extends Controller
{
    /**
     * /profile/{id}/video
     */
    public function upload(Request $request, $userId)
    {
        // validate
        $u = User::findOrFail($userId);
        // check user authorised
        if (!Auth::user()->loggedInAs()->canEditProfile($userId)) {
            throw new \App\Exceptions\UnauthorisedException();
        }

        if (!is_array($request->video)) {
            throw new \App\Exceptions\UploadFileException();
        }
        $request->validate([
            'video.*' => 'required|mimes:mp4|max:102400', // what should min dimensions be?
        ]);
        $adminOnly = $request->input('adminOnly', '0');

        $uploaded = [];
        foreach ($request->video as $i => $video) {
            // get extension
            $mimeType = $video->getMimeType();
            switch ($mimeType) {
                case 'video/mp4':
                    $ext = 'mp4';
                    break;
                default:
                    throw new \App\Exceptions\InvalidMimeException();
            }

            // store in db
            $profileVideo = new ProfileVideo();
            $profileVideo->user_id = $userId;
            $profileVideo->ext = $ext;
            $profileVideo->admin_only = $adminOnly;
            $profileVideo->save();

            // store on disk
            $targetFile = $profileVideo->id . '.' . $ext;
            $path = Storage::disk('tenant')->putFileAs('profile_videos', $request->file('video.' . $i), $targetFile, 'public');

            // generate thumbnail
            $targetThumb = TENANT_DIR . 'profile_videos/thumbs/' . $profileVideo->id . '.jpg';

            $ffmpeg = '/usr/bin/ffmpeg';
            $cmd = "$ffmpeg -i '" . TENANT_DIR . "$path' -vf scale='min(" . PHOTO_SIZE_THUMB . "\, iw):-1' -ss 5 -f image2 -vframes 1 $targetThumb 2>&1";
            exec($cmd);

            $uploaded[] = ['id' => $profileVideo->id, 'admin_only' => $profileVideo->admin_only, 'locked' => $profileVideo->locked, 'path' => $profileVideo->path(), 'thumbnail' => $profileVideo->thumbnail()];
        }

        return response()->api([
            'message' => "Uploaded.",
            'data' => $uploaded,
        ], 201);
    }

    /**
     * PUT /profileVideo/{id}/lock/{set}
     */
    public function lock($id, $set)
    {
        $v = ProfileVideo::findOrFail($id);
        if ($set) {
            $v->locked = 1;
            $a = 'locked';
        } else {
            $v->locked = 0;
            $a = 'unlocked';
        }
        $v->save();

        return response()->api([
            'data' => $v,
            'message' => "Profile video $a.",
        ], 200);
    }

    /**
     * PUT /profileVideo/{id}/adminOnly/{set}
     */
    public function adminOnly($id, $set)
    {
        $v = ProfileVideo::findOrFail($id);
        if ($set) {
            $v->admin_only = 1;
            $a = 'set';
        } else {
            $v->admin_only = 0;
            $a = 'unset';
        }
        $v->save();

        return response()->api([
            'data' => $v,
            'message' => "Profile video admin only $a.",
        ], 200);
    }
}
