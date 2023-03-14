<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\RESTResponse;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UnitController extends Controller
{
    //

    use RESTResponse;

    public function index()
    {
        $unitPaginated = Unit::paginate(10);
        return $this->setData($unitPaginated)
            ->setStatusCode(Response::HTTP_OK)
            ->setMessage('Success')
            ->successResponse();
    }

    public function show($id)
    {
        try {
            $unit = Unit::find($id);
            if (!$unit) {
                return $this->setMessage('Unit not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->errorResponse();
            }
            return $this->setData($unit)
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage('Success')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'code' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->setMessage($validator->errors()->first())
                    ->setData($validator->errors())
                    ->errorResponse();
            }

            $data = $validator->validated();

            $unit = Unit::create($data);

            return $this->setData($unit)
                ->setStatusCode(Response::HTTP_CREATED)
                ->setMessage('Success')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function update($id, Request $request)
    {
        try {
            $unit = Unit::find($id);
            if (!$unit) {
                return $this->setMessage('Unit not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->errorResponse();
            }

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string',
                'code' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->setMessage($validator->errors()->first())
                    ->setData($validator->errors())
                    ->errorResponse();
            }

            $data = $validator->validated();

            $unit->update($data);

            return $this->setData($unit)
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage('Success')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::find($id);
            if (!$unit) {
                return $this->setMessage('Unit not found')
                    ->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->errorResponse();
            }

            $unit->delete();

            return $this->setData($unit)
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage('Success')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }
}
