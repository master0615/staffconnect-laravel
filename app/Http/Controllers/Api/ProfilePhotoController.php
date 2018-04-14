<?php

namespace App\Http\Controllers\Api;

use App\ProfilePhoto;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Glide\GlideImage;

class ProfilePhotoController extends Controller
{

    /**
     * /profile/{id}/photo
     */
    public function upload(Request $request, $userId)
    {
        // validate
        $u = User::findOrFail($userId);

        // check user authorised
        if (!Auth::user()->loggedInAs()->canEditProfile($userId)) {
            throw new \App\Exceptions\UnauthorisedException();
        }

        if (!is_array($request->photo)) {
            throw new \App\Exceptions\UploadFileException();
        }
        $request->validate([
            'photo.*' => 'required|mimes:jpg,jpeg,png|dimensions:min_width=500,min_height=500|max:10240', // what should min dimensions be?
        ]);
        $adminOnly = $request->input('adminOnly', '0');

        $uploaded = [];
        foreach ($request->photo as $i => $photo) {
            // get extension
            $mimeType = $photo->getMimeType();
            switch ($mimeType) {
                case 'image/jpeg':
                    $ext = 'jpg';
                    break;
                case 'image/png':
                    $ext = 'png';
                    break;
                default:
                    throw new \App\Exceptions\InvalidMimeException();
            }

            // store in db
            $profilePhoto = new ProfilePhoto();
            $profilePhoto->user_id = $userId;
            $profilePhoto->ext = $ext;
            $profilePhoto->admin_only = $adminOnly;
            $profilePhoto->save();

            // store on disk
            $targetFile = $profilePhoto->id . '.' . $ext;
            $path = Storage::disk('tenant')->putFileAs('profile_photos', $request->file('photo.' . $i), $targetFile, 'public');

            // resize and correct orientation
            GlideImage::create(TENANT_DIR . $path)->modify([
                'w' => PHOTO_SIZE,
                'h' => PHOTO_SIZE,
                'or' => 'auto',
            ])->save(TENANT_DIR . $path);

            // create medium thumbnail
            $targetThumb = TENANT_DIR . 'profile_photos/thumbs/' . $targetFile;
            GlideImage::create(TENANT_DIR . $path)->modify([
                'w' => PHOTO_SIZE_THUMB,
                'h' => PHOTO_SIZE_THUMB,
                'fit' => 'crop-top',
            ])->save($targetThumb);

            // if user doesnt have main photo set then make this main
            if (User::find($userId)->where('ppic_a', '')) {
                $this->setMain($userId, $profilePhoto->id);
            }

            $uploaded[] = ['id' => $profilePhoto->id, 'admin_only' => $profilePhoto->admin_only, 'main' => $profilePhoto->main, 'locked' => $profilePhoto->locked, 'path' => $profilePhoto->path(), 'thumbnail' => $profilePhoto->thumbnail()];
        }

        return response()->api([
            'message' => "Uploaded.",
            'data' => $uploaded,
        ], 201);
    }

    /**
     * /profile/{userId}/photo/{profilePhotoId}
     */
    public function setMain($userId, $profile_photo_id)
    {
        $user = User::findOrFail($userId);
        $newMain = ProfilePhoto::findOrFail($profile_photo_id);

        // check user authorised
        if (!Auth::user()->loggedInAs()->canEditProfile($userId)) {
            return response()->api([
                'message' => 'Not authorised to access the user',
            ], 403);
        }

        // find existing main photo, delete set not main
        $ppic_a = $user->ppic_a;
        if ($oldMain = ProfilePhoto::where('user_id', $userId)->where('main', 1)->first()) {
            $oldMain->main = 0;
            $delete = 'profile_photos/tthumbs/' . $userId . $ppic_a;
            Storage::disk('tenant')->delete($delete);
            $oldMain->save();
        } else {
            $ppic_a = 'z'; // initialise otherwise $ppic_a[0] throws error
        }

        // increment alphabet of ppic_a for browser cache reasons
        $letter = $ppic_a[0];
        if ($letter == 'z') {
            $letter = 'a';
        } else {
            $letter = ++$letter;
        }
        $user->ppic_a = $letter . '.' . $newMain->ext;
        $user->save();

        // create new tthumb
        $targetThumb = TENANT_DIR . 'profile_photos/thumbs/' . $profile_photo_id . '.' . $newMain->ext;
        $targetTthumb = TENANT_DIR . 'profile_photos/tthumbs/' . $userId . $user->ppic_a;
        GlideImage::create($targetThumb)->modify([
            'w' => PHOTO_SIZE_TTHUMB,
            'h' => PHOTO_SIZE_TTHUMB,
            'fit' => 'crop',
        ])->save($targetTthumb);

        // set new profilePhotos main 1
        $newMain = ProfilePhoto::find($profile_photo_id); // need to re-get $newMain for some reason. ommiting this line causes main not be set every 2nd time.. weird
        $newMain->main = 1;
        $newMain->save();

        return response()->api([
            'data' => $user->ppic_a,
            'message' => "Profile photo updated.",
        ], 201);
    }

    /**
     * PUT /profilePhoto/{profilePhotoId}/lock/{set}
     */
    public function lock($id, $set)
    {
        $p = ProfilePhoto::findOrFail($id);
        if ($set) {
            $p->locked = 1;
            $a = 'locked';
        } else {
            $p->locked = 0;
            $a = 'unlocked';
        }
        $p->save();

        return response()->api([
            'data' => $p,
            'message' => "Profile photo $a.",
        ], 200);
    }

    /**
     * PUT /profilePhoto/{profilePhotoId}/adminOnly/{set}
     */
    public function adminOnly($id, $set)
    {
        $p = ProfilePhoto::findOrFail($id);
        if ($set) {
            $p->admin_only = 1;
            $a = 'set';
        } else {
            $p->admin_only = 0;
            $a = 'unset';
        }
        $p->save();

        return response()->api([
            'data' => $p,
            'message' => "Profile photo admin only $a.",
        ], 200);
    }

    /**
     * PUT /profilePhoto/{profilePhotoId}/rotate/{deg}
     */
    public function rotate($id, $deg)
    {
        if (!in_array($deg, ['90', '180', '270'])) {
            throw new \App\Exceptions\NotAllowedException();
        }

        $p = ProfilePhoto::findOrFail($id);
        $u = $p->user;
        if (!Auth::user()->loggedInAs()->canEditProfile($u->id)) {
            throw new \App\Exceptions\NotAllowedException();
        }

        //create new in db
        $p2 = $p->replicate();
        $p2->save();

        // create new image
        $targetFile = 'profile_photos/' . $p->id . '.' . $p->ext;
        $newFile = 'profile_photos/' . $p2->id . '.' . $p2->ext;
        GlideImage::create(TENANT_DIR . $targetFile)->modify([
            'or' => $deg,
        ])->save(TENANT_DIR . $newFile);

        // create medium thumbnail
        $targetThumb = 'profile_photos/thumbs/' . $p2->id . '.' . $p2->ext;
        GlideImage::create(TENANT_DIR . $newFile)->modify([
            'w' => PHOTO_SIZE_THUMB,
            'h' => PHOTO_SIZE_THUMB,
            'fit' => 'crop',
        ])->save(TENANT_DIR . $targetThumb);

        //delete old
        app('App\Http\Controllers\Api\StorageController')->deleteFile('profile_photo', $id);

        unset($p2->user);
        unset($p2->user_id);
        $p2->path = $p2->path();
        $p2->thumbnail = $p2->thumbnail();

        return response()->api([
            'data' => $p2,
            'message' => "Rotated.",
        ], 200);
    }
}
