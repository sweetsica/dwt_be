<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $kpiKeys = [];
        if ($this->kpiKeys) {
            foreach ($this->kpiKeys as $kpiKey) {
                $currentKpiKey = [
                    "id" => $kpiKey->id,
                    "name" => $kpiKey->name,
                    "description" => $kpiKey->description,
                    "unit" => $kpiKey->unit,
                    "quantity" => $kpiKey->pivot->quantity,
                ];
                array_push($kpiKeys, $currentKpiKey);
            }
        }

        return [
            "id" => $this->id,
            "targetDetail" => $this->target_detail_id,
            "note" => $this->note,
            "quantity" => $this->quantity,
            "status" => $this->status,
            "files" => $this->files,
            "noticedStatus" => $this->noticedStatus,
            "noticedDate" => $this->noticedDate,
            "reportedDate" => $this->reportedDate,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "kpiKeys" => $kpiKeys,
        ];
    }
}
