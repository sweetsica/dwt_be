<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TargetLogResource;
use App\Http\Traits\RESTResponse;
use App\Models\KpiKey;
use App\Models\TargetDetail;
use App\Models\TargetLog;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class TargetLogController extends Controller
{
    //
    use RESTResponse;

    public function index(Request $request)
    {
        try {
            $targetLogs = TargetLog::paginate(10);
            //resource
            // return TargetLogResource::collection($targetLogs);
            return $this->setMessage('Target Logs')
                ->setData(TargetLogResource::collection($targetLogs)->response()->getData(true))
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(ResponseStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function show($id)
    {
        try {
            //return with target detail and kpi keys with quantity
            $targetLog = TargetLog::query()
                ->where('id', $id)
                ->with('targetDetail')
                ->with('kpiKeys')
                ->first();

            if (!$targetLog) {
                return $this->setMessage('Target Log not found')
                    ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                    ->errorResponse();
            }
            return $this->setMessage('Target Log')
                ->setData(new TargetLogResource($targetLog))
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(ResponseStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'target_detail_id' => 'required|numeric',
                'note' => 'required|string',
                'quantity' => 'required|numeric',
                'status' => 'nullable|string',
                'files' => 'nullable|string',
                'noticedStatus' => 'nullable|string',
                'noticedDate' => 'nullable|date',
                'reportedDate' => 'required|date',
                "kpiKeys" => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return $this->setMessage($validator->errors()->first())
                    ->setData($validator->errors())
                    ->setStatusCode(ResponseStatus::HTTP_BAD_REQUEST)
                    ->errorResponse();
            }

            $newTargetLog = $validator->validate();
            $existTargetDetail = TargetDetail::find($newTargetLog['target_detail_id']);
            if (!$existTargetDetail) {
                return $this->setMessage('Target Detail not found')
                    ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                    ->errorResponse();
            }

            $targetLog = TargetLog::create($newTargetLog);
            //create target_log_kpi_key
            if ($request->kpiKeys) {
                //validate kpiKeys
                foreach ($request->kpiKeys as $kpiKey) {
                    $kpiKeyId = $kpiKey['id'];
                    $kpiKeyQuantity = $kpiKey['quantity'];
                    // error_log($kpiKeyId, 0);
                    // error_log($kpiKeyQuantity, 0);
                    if (!$kpiKeyId || !$kpiKeyQuantity) {
                        return $this->setMessage('Kpi Key or quantity is required')
                            ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                            ->errorResponse();
                    }
                    if (!is_numeric($kpiKeyId) || !is_numeric($kpiKeyQuantity)) {
                        return $this->setMessage('Kpi Key or quantity is not number')
                            ->setStatusCode(ResponseStatus::HTTP_BAD_REQUEST)
                            ->errorResponse();
                    }
                    $existKpiKey = KpiKey::find($kpiKeyId);
                    if (!$existKpiKey) {
                        return $this->setMessage('Kpi Key with id ' . $kpiKeyId . ' not found or has been deleted')
                            ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                            ->errorResponse();
                    }
                }
                foreach ($request->kpiKeys as $kpiKey) {
                    $kpiKeyId = $kpiKey['id'];
                    $kpiKeyQuantity = $kpiKey['quantity'];
                    $targetLog->kpiKeys()->attach($kpiKeyId, ['quantity' => $kpiKeyQuantity]);
                    // $targetLog = $targetLog->save();
                }
            }
            return $this->setMessage('Target Log created')
                ->setData($targetLog)
                ->setStatusCode(ResponseStatus::HTTP_CREATED)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(ResponseStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'target_detail_id' => 'nullable|numeric',
                'note' => 'nullable|string',
                'quantity' => 'nullable|numeric',
                'status' => 'nullable|string',
                'files' => 'nullable|string',
                'noticedStatus' => 'nullable|string',
                'noticedDate' => 'nullable|date',
                'reportedDate' => 'nullable|date',
                'kpiKeys' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return $this->setMessage($validator->errors()->first())
                    ->setStatusCode(ResponseStatus::HTTP_BAD_REQUEST)
                    ->errorResponse();
            }

            $updateTargetLog = $validator->validate();
            $targetLog = TargetLog::find($id);
            if (!$targetLog) {
                return $this->setMessage('Target Log not found')
                    ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                    ->errorResponse();
            }
            //check permission
            $user = auth()->user();

            if ($user->role == "user" && $user->id != $targetLog->targetDetail->user_id) {
                return $this->setMessage('You do not have permission to update this Target Log')
                    ->setStatusCode(ResponseStatus::HTTP_UNAUTHORIZED)
                    ->errorResponse();
            }

            //update target_log_kpi_key by delete all and create new
            if ($request->kpiKeys !== null) {

                //detach all kpiKeys
                $targetLog->kpiKeys()->detach();
                //validate kpiKeys
                foreach ($request->kpiKeys as $kpiKey) {
                    $kpiKeyId = $kpiKey['id'];
                    $kpiKeyQuantity = $kpiKey['quantity'];
                    // error_log($kpiKeyId, 0);
                    // error_log($kpiKeyQuantity, 0);
                    if (!$kpiKeyId || !$kpiKeyQuantity) {
                        return $this->setMessage('Kpi Key or quantity is required')
                            ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                            ->errorResponse();
                    }
                    if (!is_numeric($kpiKeyId) || !is_numeric($kpiKeyQuantity)) {
                        return $this->setMessage('Kpi Key or quantity is not number')
                            ->setStatusCode(ResponseStatus::HTTP_BAD_REQUEST)
                            ->errorResponse();
                    }
                    $existKpiKey = KpiKey::find($kpiKeyId);
                    if (!$existKpiKey) {
                        return $this->setMessage('Kpi Key with id ' . $kpiKeyId . ' not found or has been deleted')
                            ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                            ->errorResponse();
                    }
                }
                //save kpiKeys

                foreach ($request->kpiKeys as $kpiKey) {
                    $kpiKeyId = $kpiKey['id'];
                    $kpiKeyQuantity = $kpiKey['quantity'];
                    $targetLog->kpiKeys()->attach($kpiKeyId, ['quantity' => $kpiKeyQuantity]);
                }
            }

            $targetLog->update($updateTargetLog);
            return $this->setMessage('Target Log updated')
                ->setData($targetLog)
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(ResponseStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }

    public function destroy($id)
    {
        try {
            $targetLog = TargetLog::find($id);
            if (!$targetLog) {
                return $this->setMessage('Target Log not found')
                    ->setStatusCode(ResponseStatus::HTTP_NOT_FOUND)
                    ->errorResponse();
            }
            //check permission
            $user = auth()->user();
            if ($user->role != 'admin' || $user->role != 'manager') {
                if ($user->id != $targetLog->targetDetail->user_id) {
                    return $this->setMessage('You do not have permission to delete this Target Log')
                        ->setStatusCode(ResponseStatus::HTTP_UNAUTHORIZED)
                        ->errorResponse();
                }
            }

            $targetLog->delete();
            return $this->setMessage('Target Log deleted')
                ->successResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(ResponseStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        } catch (Exception $e) {
            return $this->setMessage($e->getMessage())
                ->setStatusCode(ResponseStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->errorResponse();
        }
    }
}
