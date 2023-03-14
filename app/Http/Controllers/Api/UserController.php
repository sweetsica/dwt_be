<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\RESTResponse;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use RESTResponse;
    //
    public function index(Request $request)
    {
        try {
            $q = $request->query('q');
            $limit = $request->query('limit', 10);

            if ($q) {
                $users = User::where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
                    ->paginate($limit);

                return $this->setData($users)
                    ->setMessage('Search users successfully')
                    ->successResponse();
            }
            $users = User::paginate($limit);
            return $this->setData($users)
                ->setMessage('Get users successfully')
                ->setStatusCode(Response::HTTP_OK)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Get users failed')
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function show(User $user)
    {
        try {
            if (!$user) {
                return $this->setError('User not found')
                    ->setMessage('Get user failed')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->errorResponse();
            }
            return $this->setData($user)
                ->setMessage('Get user successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Get user failed')
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function store(Request $request)
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
                'role' => 'required',
                'departement_id' => 'required|exists:departements,id',
                'position_id' => 'required|exists:positions,id',
                'position_level_id' => 'required|exists:position_levels,id',
                'salary_fund' => 'required|numeric',

            ]);
            if ($validated->fails()) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError($validated->errors())
                    ->setMessage($validated->errors()->first())
                    ->errorResponse();
            }

            $validated = $validated->validated();

            if (!in_array($validated['sex'], ['male', 'female'])) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError("Sex can only be male or female")
                    ->setMessage('Register failed: Sex can only be male or female')
                    ->errorResponse();
            }
            //only alow to create manager or user
            if (!in_array($validated['role'], ['manager', 'user'])) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError("Invalid role")
                    ->setMessage('Register failed: Invalid role')
                    ->errorResponse();
            }
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
        } catch (Exception $exception) {
            return $this->setError($exception->getMessage())
                ->setMessage('Create user failed')
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }
}
