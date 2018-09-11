<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Invitation;
use App\User;

class RegisterController extends Controller {
	public function register() {
		$invitation = Invitation::findByCode(request('invitation_code'));

		abort_if($invitation->user_id !== null, 404);

		request()->validate([
			'email' => 'required|email|unique:users',
			'password' => 'required',
		]);

		$user = User::create([
			'email' => request('email'),
			'password' => bcrypt(request('password')),
		]);

		$invitation->update(['user_id' => $user->id]);

		auth()->login($user);

		return redirect()->route('backstage.concerts.index');
	}
}
