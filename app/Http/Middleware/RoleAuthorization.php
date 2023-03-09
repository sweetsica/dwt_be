<?php

namespace App\Http\Middleware;

use App\Http\Traits\RESTResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Termwind\Components\Dd;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleAuthorization
{
    use RESTResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            //Access token from the request
            $token = JWTAuth::parseToken();
            //Try authenticating user
            $user = $token->authenticate();
        } catch (TokenExpiredException $e) {
            //Thrown if token has expired
            return $this->unauthorized('Your token has expired. Please, login again.');
        } catch (TokenInvalidException $e) {
            //Thrown if token invalid
            return $this->unauthorized('Your token is invalid. Please, login again.');
        } catch (JWTException $e) {
            //Thrown if token was not found in the request.
            return $this->unauthorized('Please, attach a Bearer Token to your request');
        }    //If user was authenticated successfully and user is in one of the acceptable roles, send to next request.

        if(!$user) {
            return $this->unauthorized('User not found');
        }
        // dd($user->role);
        //If no roles were passed, send to next request
        if($user && empty($roles)) {
            return $next($request);
        }
        //If user is in one of the acceptable roles, send to next request
        if ($user && in_array($user->role, $roles)) {
            return $next($request);
        }

        return $this->unauthorized();
    }

    private function unauthorized($message = null)
    {
        return $this->setMessage($message ?? 'You are not authorized to access this resource')
            ->setStatusCode(Response::HTTP_UNAUTHORIZED)
            ->errorResponse();
    }
}
