<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\RESTResponse;
use App\Models\PositionLevel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PositionLevelControler extends Controller
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
    public function show($id)
    {
        try {
            $position = PositionLevel::find($id);
            if (!$position) {
                return $this->setError('Position level not found')
                    ->setMessage('Get position failed')
                    ->errorResponse();
            }
            return $this->setData($position)
                ->setMessage('Get position successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Get position failed')
                ->errorResponse();
        } catch (Exception $e) {

            return $this->setError($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage('Get position failed')
                ->errorResponse();
        }
    }

    public function index(Request $request)
    {
        try {
            $q = $request->query('q');
            $limit = $request->query('limit', 10);

            if ($q) {
                $positions = PositionLevel::where('name', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    ->paginate($limit);

                return $this->setData($positions)
                    ->setMessage('Search positions successfully')
                    ->successResponse();
            }
            $positions = PositionLevel::paginate($limit);
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
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'minimum_wage' => 'nullable|numeric',
                'maximum_wage' => 'nullable|numeric',
            ]);
            if ($validator->fails()) {
                return $this->setError($validator->errors())
                    ->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setMessage($validator->errors()->first())
                    ->errorResponse();
            }
            $position = PositionLevel::create($request->all());
            return $this->setData($position)
                ->setMessage('Create position successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Create position failed')
                ->errorResponse();
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $position = PositionLevel::find($id);
            if (!$position) {
                return $this->setError('Position level not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setMessage('Update position level failed')
                    ->errorResponse();
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'minimum_wage' => 'nullable|numeric',
                'maximum_wage' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                return $this->setError($validator->errors())
                    ->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setMessage($validator->errors()->first())
                    ->errorResponse();
            }

            $position->update($request->all());
            return $this->setData($position)
                ->setMessage('Update position level successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('Update position level failed')
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function destroy($id)
    {
        try {
            $position = PositionLevel::find($id);
            if (!$position) {
                return $this->setError('Position level not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setMessage('delete position level failed')
                    ->errorResponse();
            }
            $position->delete();
            return $this->setData($position)
                ->setMessage('delete position level successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setError($e->getMessage())
                ->setMessage('delete position level failed')
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }
}
