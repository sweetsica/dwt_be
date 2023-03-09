<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\RESTResponse;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use RESTResponse;
    public function __construct()
    {
        //or auth:api is also fine
        $this->middleware('auth.role', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);
            if ($validated->fails()) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError($validated->errors())
                    ->setMessage('Login failed: validation error')
                    ->errorResponse();
            }
            $validated = $validated->validated();

            //find user by email
            $user = User::where('email', $validated['email'])->first();
            if (!$user) {
                return $this->setError('User not found')
                    ->setMessage('Login failed: Email not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->errorResponse();
            }


            // this method will check the email and password and return the token
            if (!$token = auth()->attempt($validated)) {
                return $this->setMessage('Login failed: email or password is incorrect')
                    ->setStatusCode(Response::HTTP_UNAUTHORIZED)
                    ->errorResponse();
            }
            // auth()->user() will return the user
            return $this->setData([
                'token' => $token,
                'user' => auth()->user()
            ])
                ->setMessage('User successfully logged in')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage('Login failed: ' . $e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function register(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'code' => 'required|min:6|unique:users',
                'phone' => 'required',
                'sex' => 'required',
                'address' => 'required',
                'dob' => 'required|date',
                'doj' => 'required|date',
            ]);
            if ($validated->fails()) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError($validated->errors())
                    ->setMessage('Register failed: validation error')
                    ->errorResponse();
            }

            $validated = $validated->validated();

            if(!in_array($validated['sex'], ['male', 'female'])) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                ->setError("Sex can only be male or female")
                ->setMessage('Register failed: Sex can only be male or female')
                ->errorResponse();
            }

            $validated['role'] = 'user';
            //hash the password
            $validated['password'] = bcrypt($validated['password']);

            $user = User::create($validated);
            // //this method will generate a token for the user
            // $token = auth()->login($user);

            return $this->setData([
                'user' => $user,
                // 'token' => $token
            ])
                ->setMessage('User successfully registered')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage('Register failed: ' . $e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return $this->setMessage('User successfully logged out')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage('Logout failed: ' . $e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function refresh()
    {
        try {
            $refreshedToken = auth()->refresh();
            return $this->setData([
                'token' => $refreshedToken
            ])
                ->setMessage('Token successfully refreshed')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage('Refresh token failed: ' . $e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function me()
    {
        try {
            return $this->setData(auth()->user())
                ->setMessage('User successfully fetched')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage('Fetch user failed: ' . $e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'oldPassword' => 'required|string|min:6',
                'newPassword' => 'required|string|min:6',
            ]);
            if ($validator->fails()) {
                return $this->setError($validator->errors())
                    ->setMessage('Change password failed: validation error')
                    ->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->errorResponse();
            }
            $validated = $validator->validated();

            $userId = auth()->user()->id;
            $user = User::find($userId);

            if (!$user) {
                return $this->setError('User not found')
                    ->setMessage('Change password failed: User not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->errorResponse();
            }
            //check if old password is correct
            if (!Hash::check($validated['oldPassword'], $user->password)) {
                return $this->setError('Old password is incorrect')
                    ->setMessage('Change password failed: Old password is incorrect')
                    ->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->errorResponse();
            }
            $user->password = bcrypt($validated['newPassword']);
            $user->save();
            return $this->setMessage('Password successfully changed')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage('Change password failed: ' . $e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }
}
