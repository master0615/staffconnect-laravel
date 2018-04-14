<?php
namespace App\Http\Controllers\Api;

use App\Device;
use App\Jobs\SendNotification;
use App\Message;
use App\Thread;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserChatController extends Controller
{
    /*
     * POST /chat/{id}/user/{userId}
     * add user to thread
     */
    public function addUser($id, $userId)
    {
        $t = Thread::findOrFail($id);
        $u = User::findOrFail($userId);

        if ($t->users()->where('user_id', $userId)->first()) {
            throw new \App\Exceptions\Bad(400, "The user is already in the thread.");

        } else {
            $t->users()->toggle($userId);
            $t->refreshParticipantNames();
        }

        return response()->api([
            'message' => 'Added to thread.',
        ]);
    }

    /*
     * DELETE /chat/{id}/user/{userId}
     * add user to thread
     */
    public function removeUser($id, $userId)
    {
        $t = Thread::findOrFail($id);
        $u = User::findOrFail($userId);

        if ($t->users()->where('user_id', $userId)->first()->count()) {
            $t->users()->toggle($userId);
            $t->refreshParticipantNames();

        } else {
            throw new \App\Exceptions\Bad(400, "The user is not in the thread.");
        }

        return response()->api([
            'message' => 'Removed from thread.',
        ]);
    }

    /*
     * POST /chat/message
     * send message
     */
    public function sendMessage(Request $request)
    {
        if (Auth::id() != Auth::user()->loggedInAsId()) {
            throw new \App\Exceptions\Bad(401, "You are not allowed to send messages when logged in as another user.");
        }

        $request->validate([
            'recipient_id' => "sometimes|exists:tenant.users,id|notin:" . Auth::id(),
            'thread_id' => "sometimes|exists:tenant.threads,id",
            'content' => 'required|string|min:1',
        ], [
            'content.required' => "The message content is required",
        ]);

        try {
            $sender = Auth::user()->loggedInAs();

            //make thread if not exist
            if ($request->has('thread_id')) {
                $thread = Thread::findOrFail($request->thread_id);

            } elseif ($request->has('recipient_id')) {

                $recipient = User::findOrFail($request->recipient_id);

                //check thread doesnt exist
                $existing_id = 0;
                $threads = $sender->threads()->get();
                foreach ($threads as $thread) {
                    if ($thread->users()->where('user_id', $recipient->id)->get()->count()) {
                        if ($thread->users()->whereNotIn('user_id', [$recipient->id, $sender->id])->get()->count()) {
                            continue;
                        } else {
                            $existing_id = $thread->id;
                            break;
                        }
                    }
                }

                //doesnt exist, create new
                if (!$existing_id) {
                    $thread = new Thread;
                    $thread->participant_names = $sender->name() . ', ' . $recipient->name();
                    $thread->save();

                    $thread->users()->attach($sender->id);
                    $thread->users()->attach($recipient->id);
                    //set recipient updated at to null to indicate unread
                    DB::connection('tenant')->table('thread_user')->where([['thread_id', $thread->id], ['user_id', $recipient->id]])->update(['updated_at' => null]);
                }

            } else {
                throw new \App\Exceptions\Bad(400, "Either recipient_id or thread_id is required.");
            }

            $message = new Message();
            $message->sender_id = $sender->id;
            $message->thread_id = $thread->id;
            $message->content = $request->content;
            $message->save();

            $devices = Device::whereIn('user_id', $thread->users()->select('users.id')->where('user_id', '!=', $sender->id)->pluck('id')->all())->get();

            /* Send FCM notification */
            $devices->each(function ($device) use ($sender, $message) {
                dispatch(new SendNotification($device->firebase_token, $sender, $message));
            });

            //update sender thread read datetime
            $thread->users()->updateExistingPivot($sender->id, ['updated_at' => date('Y-m-d H:i:s')]);

            //update thread
            $thread->updated_at = date('Y-m-d H:i:s');
            $thread->save();

            return response()->api($message);

        } catch (\Exception $e) {
            return response()->api([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /*
     * GET /chat/thread/{id}/{pageSize?}/{pageNumber?}
     * get messages in thread
     */
    public function getMessages($id, $pageNumber = 0)
    {
        $pageSize = 10;
        $thread = Thread::with('users:users.id')->findOrFail($id);

        //store pivot updated_at to work out who's seen what message
        $userUpdates = [];
        foreach ($thread->users as $u) {
            $userUpdates[$u->id] = $u->pivot->updated_at;
        }
        if (isset($userUpdates[Auth::user()->loggedInAsId()])) {
            //no need to display your own picture
            unset($userUpdates[Auth::user()->loggedInAsId()]);
        } else {
            throw new \App\Exception\Bad(401, "You are not allowed to view this thread.");
        }

        $messages = $thread->messages()
            ->select('id', 'content', 'sender_id', 'created_at')
            ->orderBy('created_at', 'desc');
        if ($pageNumber) {
            $messages = $messages->skip($pageSize * $pageNumber);
        }
        $messages = $messages->take($pageSize)
            ->get();

        //TODO need to deal with seen by ids with pagination
        foreach ($messages as $m) {
            $seenByIds = [];
            foreach ($userUpdates as $user_id => $t) {
                if (strtotime($m->created_at) <= strtotime($t)) {
                    $seenByIds[] = $user_id;
                    unset($userUpdates[$user_id]);
                }
            }
            $m->seen_by_ids = $seenByIds;
            $m->ppic_a = $m->sender->tthumb();
            unset($m->sender);
        }

        //reverse after paginate
        $messages = $messages->reverse()->values();

        return response()->api($messages);
    }

    /*
     * GET /chat/threads
     * get users threads
     */
    public function getThreads()
    {
        $limit = 10;

        $threads = Auth::user()->loggedInAs()->threads()
            ->with('users:users.id,fname,lname,sex,ppic_a')
            ->select('threads.id', 'tname', 'participant_names', 'threads.updated_at')
            ->orderBy('threads.updated_at', 'desc')
            ->take($limit)
            ->get();

        $threads = $threads->map(function ($item, $key) {
            if ($item->tname == '') {
                $item->name = str_ireplace(Auth::user()->loggedInAs()->name(), '', $item->participant_names);
                $item->name = ltrim($item->name, ', ');
                $item->name = str_ireplace(', ,', ', ', $item->name);
                $item->name = rtrim($item->name, ', ');

            } else {
                $item->name = $item->tname;
            }

            //calculate unread
            $last_read = $item->pivot->updated_at;
            $item->unread = $item->messages()->where('created_at', '>', $last_read)->get()->count();

            //thumb
            $item->ppic_a = $item->tthumb(Auth::user()->loggedInAsId());

            //participants
            $participants = [];
            foreach ($item->users as $u) {
                $participants[] = ['id' => $u->id, 'name' => $u->fname . ' ' . $u->lname, 'ppic_a' => $u->tthumb()];
            }
            $item->participants = $participants;

            unset($item->users);
            unset($item->tname);
            unset($item->participant_names);
            unset($item->pivot);
            return $item;
        });

        return response()->api($threads);
    }

    /*
     * GET /chat/unread/{id}
     * get unread messages in thread
     */
    public function getUnreadMessages($id)
    {
        $thread = Thread::findOrFail($id);
        $messages = [];

        $last_read = $thread->users()->where('user_id', Auth::user()->loggedInAsId())->first();
        if ($last_read) {
            $last_read = $last_read->pivot->updated_at;
            $messages = $thread->messages()
                ->select('id', 'content', 'sender_id', 'created_at')
                ->where('created_at', '>', $last_read)
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($messages as $m) {
                $m->ppic_a = $m->sender->tthumb();
                unset($m->sender);
            }
        }
        return response()->api($messages);
    }

    /*
     * PUT /chat/thread/{id}/read
     * store datetime of last read into thread_user pivot
     */
    public function threadRead($id)
    {
        if (Auth::id() != Auth::user()->loggedInAsId()) {
            return response()->api();
        }

        $thread = Thread::findOrFail($id);
        $thread->users()->updateExistingPivot(Auth::id(), ['updated_at' => date('Y-m-d H:i:s')]);

        return response()->api([
            'message' => 'updated seen',
        ]);
    }

    /*
     * PUT /chat/thread/{id}
     */
    public function updateThread(Request $request, $id)
    {
        $thread = Thread::findOrFail($id);
        $request->validate([
            'tname' => "required|nullable|string",
        ]);
        $thread->tname = $request->tname;
        $thread->save();

        return response()->api([
            'message' => 'Saved.',
        ]);
    }
}
