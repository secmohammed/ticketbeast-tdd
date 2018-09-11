<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConcertAdded {
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $concert;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($concert) {
		$this->concert = $concert;
	}
}
