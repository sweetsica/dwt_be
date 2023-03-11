<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\RESTResponse;
use App\Models\Target;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TargetController extends Controller
{

    use RESTResponse;

    public function index(Request $request)
    {
        try {
            $q = $request->query('q');
            $limit = $request->query('limit', 10);
            if ($q) {
                $targets = Target::where('name', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    // ->with('targetDetails')
                    ->paginate($limit);

                return $this->setStatusCode(Response::HTTP_OK)
                    ->setData($targets)
                    ->setMessage('Search targets successfully')
                    ->successResponse();
            }
            // $targets = Target::with('targetDetails')->paginate(10);
            $targets = Target::paginate($limit);
            return $this->setStatusCode(Response::HTTP_OK)
                ->setData($targets)
                ->setMessage('Get targets successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setError($e->getMessage())
                ->setMessage('Get targets failed')
                ->errorResponse();
        }
    }

    public function show($id)
    {
        try {
            $target = Target::find($id);
            if (!$target) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setError('Target not found')
                    ->setMessage('Get target failed')
                    ->errorResponse();
            }
            return $this->setStatusCode(Response::HTTP_OK)
                ->setData($target)
                ->setMessage('Get target successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setError($e->getMessage())
                ->setMessage('Get target failed')
                ->errorResponse();
        }
    }

    public function store()
    {
        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'required',
                'manday' => 'required|numeric',
                'description' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError($validator->errors())
                    ->setMessage($validator->errors()->first())
                    ->errorResponse();
            }
            $data = $validator->validated();
            $target = Target::create($data);
            return $this->setStatusCode(Response::HTTP_OK)
                ->setData($target)
                ->setMessage('Create target successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setError($e->getMessage())
                ->setMessage('Create target failed')
                ->errorResponse();
        }
    }

    public function update($id)
    {
        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'nullable',
                'manday' => 'numeric|nullable',
                'description' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError($validator->errors())
                    ->setMessage($validator->errors()->first())
                    ->errorResponse();
            }
            $data = $validator->validated();
            $target = Target::find($id);
            if (!$target) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setError('Target not found')
                    ->setMessage('Update target failed')
                    ->errorResponse();
            }
            $target->update($data);
            return $this->setStatusCode(Response::HTTP_OK)
                ->setData($target)
                ->setMessage('Update target successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setError($e->getMessage())
                ->setMessage('Update target failed')
                ->errorResponse();
        }
    }

    public function destroy($id)
    {
        try {
            $target = Target::find($id);
            if (!$target) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setError('Target not found')
                    ->setMessage('Delete target failed')
                    ->errorResponse();
            }
            $target->delete();
            return $this->setStatusCode(Response::HTTP_OK)
                ->setData($target)
                ->setMessage('Delete target successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setError($e->getMessage())
                ->setMessage('Delete target failed')
                ->errorResponse();
        }
    }
}
