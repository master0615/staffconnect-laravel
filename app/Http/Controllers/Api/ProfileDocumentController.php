<?php

namespace App\Http\Controllers\Api;

use App\ProfileDocument;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Glide\GlideImage;

class ProfileDocumentController extends Controller
{
    /**
     * /profile/{id}/document
     */
    public function upload(Request $request, $userId)
    {
        // validate
        $u = User::findOrFail($userId);

        // check user authorised
        if (!Auth::user()->loggedInAs()->canEditProfile($userId)) {
            throw new \App\Exceptions\UnauthorisedException();
        }

        if (!is_array($request->document)) {
            throw new \App\Exceptions\UploadFileException();
        }

        $request->validate([
            'document.*' => 'required|max:10240', // large size for big pdfs? openoffice docs becore octet-streams so don't user required mime types
        ]);
        $adminOnly = $request->input('adminOnly', '0');

        $uploaded = [];
        foreach ($request->document as $i => $document) {
            // get extension
            // $ext=$request->document->extension(); returns 'bin' for docx created by openoffice
            $ext = $document->getClientOriginalExtension();

            if (!in_array($ext, ['doc', 'docx', 'pdf'])) {
                throw new \App\Exceptions\InvalidMimeException();
            }

            // store in db
            $profileDocument = new ProfileDocument();
            $profileDocument->user_id = $userId;
            $profileDocument->ext = $ext;
            $profileDocument->admin_only = $adminOnly;
            $profileDocument->save();

            // store on disk
            $targetFile = $profileDocument->id . '.' . $ext;
            $path = Storage::disk('tenant')->putFileAs('profile_documents', $request->file('document.' . $i), $targetFile, 'public');

            if ($ext == 'jpg' || $ext == 'png') {
                // make thumbnail
                $targetThumb = TENANT_DIR . 'profile_documents/thumbs/' . $targetFile;
                GlideImage::create(TENANT_DIR . $path)->modify([
                    'w' => PHOTO_SIZE_THUMB,
                    'h' => PHOTO_SIZE_THUMB,
                    'fit' => 'crop',
                ])->save($targetThumb);
            }

            $uploaded[] = ['id' => $profileDocument->id, 'admin_only' => $profileDocument->admin_only, 'locked' => $profileDocument->locked, 'path' => $profileDocument->path(), 'thumbnail' => $profileDocument->thumbnail()];
        }

        return response()->api([
            'message' => "Uploaded.",
            'data' => $uploaded,
        ], 201);
    }

    /**
     * PUT /profileDocument/{id}/lock/{set}
     */
    public function lock($id, $set)
    {
        $d = ProfileDocument::findOrFail($id);
        if ($set) {
            $d->locked = 1;
            $a = 'locked';
        } else {
            $d->locked = 0;
            $a = 'unlocked';
        }
        $d->save();

        return response()->api([
            'data' => $d,
            'message' => "Profile document $a.",
        ], 200);
    }

    /**
     * PUT /profileDocument/{id}/adminOnly/{set}
     */
    public function adminOnly($id, $set)
    {
        $d = ProfileDocument::findOrFail($id);
        if ($set) {
            $d->admin_only = 1;
            $a = 'set';
        } else {
            $d->admin_only = 0;
            $a = 'unset';
        }
        $d->save();

        return response()->api([
            'data' => $d,
            'message' => "Profile document admin only $a.",
        ], 200);
    }
}
