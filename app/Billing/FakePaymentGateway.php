<?php

namespace App\Billing;

use App\Billing\PaymentFailedException;

class FakePaymentGateway implements PaymentGateway {
	const TEST_CARD_NUMBER = '4242424242424242';
	private $charges;
	private $tokens;
	private $beforeFirstChargeCallback;

	public function __construct() {
		$this->charges = collect();
		$this->tokens = collect();
	}

	public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER) {
		$token = 'fake-tok_' . str_random(24);
		$this->tokens[$token] = $cardNumber;

		return $token;
	}

	public function charge($amount, $token, $desinationAccountId) {
		if ($this->beforeFirstChargeCallback !== null) {
			$callback = $this->beforeFirstChargeCallback;
			$this->beforeFirstChargeCallback = null;
			$callback($this);
		}

		if (!$this->tokens->has($token)) {
			throw new PaymentFailedException;
		}

		return $this->charges[] = new Charge([
			'amount' => $amount,
			'card_last_four' => substr($this->tokens[$token], -4),
			'destination' => $desinationAccountId,
		]);
	}

	public function totalCharges() {
		return $this->charges->map->amount()->sum();
	}

	public function totalChargesFor($accountId) {
		return $this->charges->filter(function ($charge) use ($accountId) {
			return $charge->destination() == $accountId;
		})->map->amount()->sum();
	}

	public function beforeFirstCharge($callback) {
		$this->beforeFirstChargeCallback = $callback;
	}

	public function newChargesDuring($callback) {
		$chargesFrom = $this->charges->count();
		$callback($this);

		return $this->charges->slice($chargesFrom)->reverse()->values();
	}
}
