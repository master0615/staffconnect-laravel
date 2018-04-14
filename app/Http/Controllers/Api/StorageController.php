<?php
namespace App\Http\Controllers\Api;

use App\ProfileDocument;
use App\ProfilePhoto;
use App\ProfileVideo;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{

    /**
     * /file/{fileType}/{id}/{ext}/{thumbnail?}
     */
    public function getFile($type, $id, $ext, $thumbnail = false) // TODO auth check. some public, some private

    {
        switch ($type) {

            case 'profile_document':
                if ($thumbnail == 1) {
                    if ($ext == 'jpg' || $ext == 'png') {
                        $target = 'profile_documents/thumbs/' . $id . '.' . $ext;
                    } else {
                        $target = 'generic icon'; // TODO path to icon
                    }
                } else {
                    $target = 'profile_documents/' . $id . '.' . $ext;
                }
                break;

            case 'profile_photo':
                if ($thumbnail == 1) {
                    $target = 'profile_photos/thumbs/' . $id . '.' . $ext;
                } elseif ($thumbnail == 2) {
                    $target = 'profile_photos/tthumbs/' . $id . $ext; // tthumb uses user->ppic_a
                } else {
                    $target = 'profile_photos/' . $id . '.' . $ext;
                }
                break;

            case 'profile_video':
                if ($thumbnail == 1) {
                    $target = 'profile_videos/thumbs/' . $id . '.jpg';
                } else {
                    $target = 'profile_videos/' . $id . '.' . $ext;
                }
                break;

            default:
                abort(500);
        }

        if (Storage::disk('tenant')->exists($target)) {

            $file = Storage::disk('tenant')->get($target);
            $mimeType = Storage::disk('tenant')->getMimeType($target);

            return response($file)->header('Content-Type', $mimeType);

        } elseif ($thumbnail) {
            switch ($ext) {
                case 'doc':
                case 'docx':
                    $file = 'images/file_doc.gif';
                    break;

                case 'pdf':
                    $file = 'images/file_pdf.gif';
                    break;

                case 'xls':
                case 'xlsx':
                    $file = 'images/file_xls.gif';
                    break;

                default:
                    $file = 'images/file.gif';

            }
            $file = Storage::disk('public')->get($file);
            return response($file)->header('Content-Type', 'image/gif');
        }
        abort(404);
    }

    /**
     * /file/{fileType}/{id}
     */
    public function deleteFile($type, $id)
    {
        switch ($type) {
            case 'profile_document':
                $file = ProfileDocument::findOrFail($id);
                $ext = $file->ext;
                $delete = array(
                    'profile_documents/' . $id . '.' . $ext,
                );
                if ($ext == 'jpg' || $ext == 'png') {
                    $delete[] = 'profile_documents/thumbs/' . $id . '.' . $ext;
                }
                break;

            case 'profile_photo': // tthumb not affected
                $file = ProfilePhoto::findOrFail($id);
                $ext = $file->ext;
                $delete = array(
                    'profile_photos/' . $id . '.' . $ext,
                    'profile_photos/thumbs/' . $id . '.' . $ext,
                );
                break;

            case 'profile_video':
                $file = ProfileVideo::findOrFail($id);
                $ext = $file->ext;
                $delete = array(
                    'profile_videos/' . $id . '.' . $ext,
                    'profile_videos/thumbs/' . $id . '.jpg',
                );
                break;

            default:
                return response()->json([
                    'message' => 'Invalid type',
                ], 500);
        }
        Storage::disk('tenant')->delete($delete);
        $file->delete();
        return response()->api([
            'message' => 'Deleted',
        ], 200);
    }
}
