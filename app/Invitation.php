<?php

namespace App;

use App\Mail\InvitationEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Invitation extends Model {
	protected $guarded = [];

	public static function findByCode($code) {
		return static::where('code', $code)->firstOrFail();
	}

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function hasBeenUsed() {
		return $this->user_id !== null;
	}

	public function send() {
		Mail::to($this->email)->send(new InvitationEmail($this));
	}
}
