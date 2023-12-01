<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Mail\VerificationMail;
use App\Models\User;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use CommonTrait;

    public function register(UserRegisterRequest $request)
    {
        try {
            $request = $request->validated();
            $request['remember_token'] = sha1(time());
            $request['password'] = bcrypt($request['password']);
            User::create($request);

            Mail::to($request['email'])->send(new VerificationMail($request));

            return $this->sendSuccess("User registered successfully.", true);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

    public function verify($code)
    {
        try {
            $user = User::where('remember_token', $code)->first();
            if (!$user) {
                return $this->sendError('Invalid verification code.', 404);
            }

            $user->update([
                'email_verified_at' => now(),
                'remember_token' => null
            ]);
            return $this->sendSuccess("Email verified successfully.", true);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

    public function login(UserLoginRequest $request)
    {
        try {

            $request->validated();
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials) && Auth::user()->email_verified_at != null) {
                $user = User::where('email', $request->email)->first();
                $token = $user->createToken('myapptoken')->plainTextToken;

                return $this->sendSuccess("User Logged In successfully.", ['user' => $user, 'token' => $token]);
            } else {
                return $this->sendError('Something went wrong.', 404);
            }

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();

            return $this->sendSuccess('Logout Successfully', true);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), null);
        }
    }
}
