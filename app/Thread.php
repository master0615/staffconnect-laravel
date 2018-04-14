<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use UsesTenantConnection;

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function refreshParticipantNames()
    {
        $names = [];
        $participants = $this->users()->get();
        foreach ($participants as $u) {
            $names[] = $u->name();
        }
        $this->participant_names = implode(', ', $names);
        $this->save();
    }

    public function tthumb($userId)
    {
        //get thread thumbnail
        $participants = $this->users()->where('user_id', '!=', $userId)->get();
        if ($participants->count() == 1) {
            return $participants[0]->tthumb();
        }

        //get last message not from user
        $m = $this->messages()->where('sender_id', '!=', $userId)->orderBy('created_at',
            'desc')->first();
        if ($m) {
            return $m->sender->tthumb();
        }

        //get random user in thread
        return $participants[0]->tthumb();
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }
}
