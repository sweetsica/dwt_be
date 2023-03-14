<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\RESTResponse;
use App\Models\Position;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PositionControler extends Controller
{
    //
    use RESTResponse;
    public function __construct()
    {
        //only admin and  manager can create update delete
        $this->middleware('auth.role:admin,manager', ['except' => ['index', 'show']]);
        //user can only get and show
        $this->middleware('auth.role:user,admin,manager', ['only' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        try {
            $q = $request->query('q');
            $limit = $request->query('limit', 10);

            if ($q) {
                $positions = Position::where('name', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    ->paginate($limit);

                return $this->setData($positions)
                    ->setMessage('Search positions successfully')
                    ->successResponse();
            }
            $positions = Position::paginate($limit);
            return $this->setData($positions)
                ->setMessage('Get positions successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Get positions failed')
                ->errorResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage('Get departments failed')
                ->errorResponse();
        }
    }

    public function show($id)
    {
        try {
            $position = Position::find($id);
            if (!$position) {
                return $this->setError('Position not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setMessage('Get position failed')
                    ->errorResponse();
            }

            return $this->setData($position)
                ->setMessage('Get position successfully')
                ->setStatusCode(Response::HTTP_OK)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Get position failed')
                ->errorResponse();
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'parent' => 'nullable|numeric',
                'description' => 'nullable',
                'salary_fund' => 'nullable|numeric',
                'max_employees' => 'nullable|integer',
            ]);
            if ($validator->fails()) {
                return $this->setError($validator->errors())
                    ->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setMessage($validator->errors()->first())
                    ->errorResponse();
            }
            $position = Position::create($validator->validated());
            return $this->setData($position)
                ->setMessage('Create position successfully')
                ->setStatusCode(Response::HTTP_CREATED)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage('Get departments failed')
                ->errorResponse();
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'parent' => 'nullable|numeric',
                'description' => 'nullable',
                'salary_fund' => 'nullable|numeric',
                'max_employees' => 'nullable|integer',
            ]);
            if ($validator->fails()) {
                return $this->setError($validator->errors())
                    ->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setMessage($validator->errors()->first())
                    ->errorResponse();
            }
            $position = Position::find($id);
            if (!$position) {
                return $this->setError('Position not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setMessage('Update position failed')
                    ->errorResponse();
            }
            $position->update($validator->validated());
            return $this->setData($position)
                ->setMessage('Update position successfully')
                ->setStatusCode(Response::HTTP_OK)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage('Get departments failed')
                ->errorResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage('Get departments failed')
                ->errorResponse();
        }
    }

    public function destroy($id)
    {
        try {

            $position = Position::find($id);
            if (!$position) {
                return $this->setError('Position not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setMessage('Delete position failed')
                    ->errorResponse();
            }
            $position->delete();
            return $this->setData($position)
                ->setMessage('Delete position successfully')
                ->setStatusCode(Response::HTTP_OK)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage('delete departments failed')
                ->errorResponse();
        }
    }
}
