<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\RESTResponse;
use App\Models\KpiKey;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class KpiKeyController extends Controller
{
    //
    use RESTResponse;

    public function index(Request $request)
    {
        try {
            $q = $request->query('q');
            $limit = $request->query('limit', 10);
            $kpiKeys = KpiKey::query();
            if ($q) {
                $kpiKeys
                    ->where('name', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            }
            $kpiKeys = $kpiKeys->with('unit')->paginate($limit);
            return $this->setStatusCode(Response::HTTP_OK)
                ->setMessage('Kpi keys retrieved successfully')
                ->setData($kpiKeys)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage($e->getMessage())
                ->errorResponse();
        }
    }

    public function show($id)
    {
        try {

            $kpiKey = KpiKey::find($id);
            if (!$kpiKey) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setMessage('Kpi key not found or has been deleted')
                    ->errorResponse();
            }
            return $this->setStatusCode(Response::HTTP_OK)
                ->setMessage('Kpi key retrieved successfully')
                ->setData($kpiKey->with('unit')->first())
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage($e->getMessage())
                ->errorResponse();
        }
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'nullable|string',
                'unit_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->setMessage($validator->errors()->first())
                    ->setData($validator->errors()->first())
                    ->errorResponse();
            }
            $unit = Unit::find($request->unit_id);
            if (!$unit) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setMessage('Unit not found or has been deleted')
                    ->errorResponse();
            }
            $data = $validator->validated();
            $kpiKey = KpiKey::create($data);
            return $this->setStatusCode(Response::HTTP_CREATED)
                ->setMessage('Kpi key created successfully')
                ->setData($kpiKey)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage($e->getMessage())
                ->errorResponse();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string',
                'description' => 'nullable|string',
                'unit_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return $this->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                    ->setMessage($validator->errors()->first())
                    ->setData($validator->errors())
                    ->errorResponse();
            }
            if ($request->unit_id) {
                $unit = Unit::find($request->unit_id);
                if (!$unit) {
                    return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                        ->setMessage('Unit not found or has been deleted')
                        ->errorResponse();
                }
            }
            $data = $validator->validated();
            $kpiKey = KpiKey::find($id);
            if (!$kpiKey) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setMessage('Kpi key not found or has been deleted')
                    ->errorResponse();
            }
            $kpiKey->update($data);
            return $this->setStatusCode(Response::HTTP_OK)
                ->setMessage('Kpi key updated successfully')
                ->setData($kpiKey)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage($e->getMessage())
                ->errorResponse();
        }
    }

    public function destroy($id)
    {
        try {
            $kpiKeyToDel = KpiKey::find($id);
            if (!$kpiKeyToDel) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setMessage('Kpi key not found or has been deleted')
                    ->errorResponse();
            }
            $kpiKeyToDel->delete();
            return $this->setStatusCode(Response::HTTP_OK)
                ->setMessage('Kpi key deleted successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setMessage($e->getMessage())
                ->errorResponse();
        }
    }
}
