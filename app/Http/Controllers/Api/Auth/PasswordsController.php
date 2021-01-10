<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\ForgotPasswordRequested;
use App\Events\PasswordRecovered;
use App\Exceptions\EmailNotSentException;
use App\Exceptions\PasswordNotUpdated;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordsController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
        ]);

        $response = $this->broker()->sendResetLink(['email' => $request->get('email')]);

        if ($response == Password::RESET_LINK_SENT) {
            event(new ForgotPasswordRequested($request->get('email')));

            return response()->json(null, 201);
        }

        throw new EmailNotSentException(__($response));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|exists:password_resets,token',
            'password' => 'required|min:8',
        ]);

        $response = $this->broker()->reset($request->except('_token'), function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        if ($response == Password::PASSWORD_RESET) {
            event(new PasswordRecovered(User::where('email', $request->get('email'))->first()));

            return response()->json([
                'message' => __($response),
            ]);
        }

        throw new PasswordNotUpdated(__($response));
    }

    public function broker()
    {
        return Password::broker();
    }
}
