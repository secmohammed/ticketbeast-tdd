<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use App\Jobs\SendAttendeeMessage;
use Illuminate\Http\Request;

class ConcertMessagesController extends Controller {
	public function create($id) {
		$concert = auth()->user()->concerts()->findOrFail($id);

		return view('backstage.concert-messages.create', compact('concert'));
	}

	public function store($id) {
		$concert = auth()->user()->concerts()->findOrFail($id);

		$this->validate(request(), [
			'subject' => 'required',
			'message' => 'required',
		]);

		$message = $concert->attendeeMessages()->create(request(['subject', 'message']));

		SendAttendeeMessage::dispatch($message);

		return redirect()->route('backstage.concert-messages.create', $concert)
			->with(['flash' => 'Your message has been sent.']);
	}
}
