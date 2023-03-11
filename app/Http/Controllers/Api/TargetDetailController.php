<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TargetDetailResource;
use App\Http\Traits\RESTResponse;
use App\Models\Target;
use App\Models\TargetDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TargetDetailController extends Controller
{
    use RESTResponse;

    public function index(Request $request)
    {
        try {
            $q = $request->query('q');
            $user_id = $request->query('user_id');
            $status = $request->query('status');

            $limit = $request->query('limit', 10);


            $targetDetails = TargetDetail::query();

            if ($q) {
                $targetDetails
                    ->where('name', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            }
            if ($user_id) {
                $targetDetails->where('user_id', $user_id);
            }
            if ($status) {
                if ($status == 'assigned') {
                    //user_id is not null
                    $targetDetails->whereNotNull('user_id');
                } else if ($status == 'unassigned') {
                    //user_id is null
                    $targetDetails->whereNull('user_id');
                }
            }

            $targetDetailsPagianted = $targetDetails
                ->with('target')
                ->with('user')
                ->with('targetLogs')
                ->with('targetLogs.kpiKeys')
                ->paginate($limit);

            return $this->setData(TargetDetailResource::collection($targetDetailsPagianted)->response()->getData(true))
                ->setStatusCode(Response::HTTP_OK)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setErrorMessage($e->getMessage())
                ->errorResponse();
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'nullable',
                'target_id' => 'required|numeric',
                'user_id' => 'nullable|numeric|exists:users,id',
                'executionPlan' => 'nullable',
                'status' => 'nullable',
                'quantity' => 'required|numeric',
                'manday' => 'required|numeric',
                'startDate' => 'required|date',
                'deadline' => 'required|date',
                'managerComment' => 'nullable',
                'managerManDay' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError($validator->errors())
                    ->setMessage($validator->errors()->first())
                    ->errorResponse();
            }
            $target = Target::find($request->target_id);
            if (!$target) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setError('Target not found or deleted')
                    ->setMessage('Create target detail failed')
                    ->errorResponse();
            }

            $newTargetDetail = $validator->validate();

            $targetDetail = TargetDetail::create($newTargetDetail);

            return $this->setData($targetDetail)
                ->setStatusCode(Response::HTTP_CREATED)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setError($e->getMessage())
                ->errorResponse();
        }
    }

    public function show($id)
    {
        try {
            $targetDetail = TargetDetail::find($id);

            if (!$targetDetail) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setError('Target detail not found or deleted')
                    ->setMessage('Get target detail failed')
                    ->errorResponse();
            }
            $res = $targetDetail->load('target')->load('user')->load('targetLogs')->load('targetLogs.kpiKeys');
            return $this->setData(new TargetDetailResource($res))
                ->setStatusCode(Response::HTTP_OK)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setError($e->getMessage())
                ->errorResponse();
        }
    }

    public function update($id)
    {
        try {
            $validator = Validator::make(request()->all(), [
                'name' => 'nullable',
                'description' => 'nullable',
                'target_id' => 'nullable|numeric',
                'user_id' => 'nullable|numeric|exists:users,id',
                'executionPlan' => 'nullable',
                'status' => 'nullable',
                'quantity' => 'nullable|numeric',
                'manday' => 'nullable|numeric',
                'startDate' => 'nullable|date',
                'deadline' => 'nullable|date',
                'managerComment' => 'nullable',
                'managerManDay' => 'nullable',
            ]);
            if ($validator->fails()) {
                return $this->setStatusCode(Response::HTTP_BAD_REQUEST)
                    ->setError($validator->errors())
                    ->setMessage($validator->errors()->first())
                    ->errorResponse();
            }

            if (request()->target_id) {
                $target = Target::find(request()->target_id);
                if (!$target) {
                    return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                        ->setError('Target not found or deleted')
                        ->setMessage('Create target detail failed')
                        ->errorResponse();
                }
            }

            $targetDetail = TargetDetail::find($id);

            if (!$targetDetail) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setError('Target detail not found or deleted')
                    ->setMessage('Update target detail failed')
                    ->errorResponse();
            }
            $data = $validator->validate();
            $targetDetail->update($data);

            return $this->setData($targetDetail)
                ->setStatusCode(Response::HTTP_OK)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setError($e->getMessage())
                ->errorResponse();
        }
    }

    public function destroy($id)
    {
        try {
            $targetDetail = TargetDetail::find($id);

            if (!$targetDetail) {
                return $this->setStatusCode(Response::HTTP_NOT_FOUND)
                    ->setError('Target detail not found or deleted')
                    ->setMessage('Delete target detail failed')
                    ->errorResponse();
            }

            $targetDetail->delete();

            return $this->setData($targetDetail)
                ->setStatusCode(Response::HTTP_OK)
                ->setMessage('Delete target detail successfully')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
                ->setError($e->getMessage())
                ->errorResponse();
        }
    }
}
