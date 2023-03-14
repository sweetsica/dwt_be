<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "target" => $this->target,
            "user" => $this->user,
            "name" => $this->name,
            "description" => $this->description,
            "executionPlan" => $this->executionPlan,
            "status" => $this->status,
            "quantity" => $this->quantity,
            "manday" => $this->manday,
            "startDate" => $this->startDate,
            "deadline" => $this->deadline,
            "managerComment" => $this->managerComment,
            "managerManDay" => $this->managerManDay,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "targetLogs" =>  TargetLogResource::collection($this->targetLogs),
        ];
    }
}
