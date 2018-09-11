<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Exceptions\NotEnoughTicketsRemainException;
use App\Mail\OrderConfirmationEmail;
use Illuminate\Support\Facades\Mail;

class ConcertOrdersController extends Controller {
	private $paymentGateway;

	public function __construct(PaymentGateway $paymentGateway) {
		$this->paymentGateway = $paymentGateway;
	}

	public function store($concertId) {
		$concert = Concert::published()->findOrFail($concertId);

		$this->validate(request(), [
			'email' => 'required|email',
			'ticket_quantity' => 'required|min:1|integer',
			'payment_token' => 'required',
		]);

		try {
			$reservation = $concert->reserveTickets(request('ticket_quantity'), request('email'));

			$order = $reservation->complete($this->paymentGateway, request('payment_token'), $concert->user->stripe_account_id);

			Mail::to($order->email)->send(new OrderConfirmationEmail($order));

			return response()->json($order, 201);
		} catch (PaymentFailedException $e) {
			$reservation->cancel();
			return response(['Damn'], 422);
		} catch (NotEnoughTicketsRemainException $e) {
			return response([], 422);
		}
	}
}
