<?php

namespace App\Models;
use Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    protected $table ="matches";
    protected $appends = ['otherUser'];

    public function getOtherUserAttribute() {
    	if (!Auth::check()) return [];

    	$myUserId = Auth::user()->id;

    	if ($this->user1 == $myUserId) {
    		return User::where('id', $this->user2)->get()->first();
    	} else if ($this->user2 == $myUserId) {
    		return User::where('id', $this->user1)->get()->first();
    	}
    	return [];
    }
}
