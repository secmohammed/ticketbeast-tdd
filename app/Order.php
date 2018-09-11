<?php

namespace App;

use App\Billing\Charge;
use App\Concert;
use App\Facades\OrderConfirmationNumber;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
	protected $guarded = [];

	public static function forTickets($tickets, $email, Charge $charge) {
		$order = static::create([
			'confirmation_number' => OrderConfirmationNumber::generate(),
			'email' => $email,
			'amount' => $charge->amount(),
			'card_last_four' => $charge->cardLastFour(),
		]);

		$tickets->each->claimFor($order);

		return $order;
	}

	public static function findByConfirmationNumber($confirmationNumber) {
		return static::where('confirmation_number', $confirmationNumber)->firstOrFail();
	}

	public function toArray() {
		return [
			'email' => $this->email,
			'amount' => $this->amount,
			'confirmation_number' => $this->confirmation_number,
			'tickets' => $this->tickets->map(function ($ticket) {
				return ['code' => $ticket->code];
			})->all(),
		];
	}

	public function tickets() {
		return $this->hasMany(Ticket::class);
	}

	public function concert() {
		return $this->belongsTo(Concert::class);
	}

	public function ticketQuantity() {
		return $this->tickets()->count();
	}

	public function getFormattedAmountAttribute() {
		return number_format($this->amount / 100, 2);
	}

	public function getFormattedDateAttribute() {
		return $this->created_at->format('F d Y, g:ia');
	}
}
