<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\RESTResponse;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    use RESTResponse;

    public function index(Request $request)
    {
        return $this->setMessage('Server is running ' . 'php ' . phpversion() . ' ' . 'laravel ' . app()->version())
            ->successResponse();
    }

    public function admin()
    {
        return $this->setMessage('Admin authenticated')->successResponse();
    }

    public function user()
    {
        return $this->setMessage('User authenticated')->successResponse();
    }
}
