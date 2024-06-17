<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="TaskResource")
 * {
 *
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     description="The task Id."
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="The task name."
 *   ),
 *   @OA\Property(
 *     property="uuid",
 *     type="string",
 *     description="The task uuid."
 *   ),
 *  @OA\Property(
 *      property="activityId",
 *      type="string",
 *      description="The task activityId."
 *    ),
 *   @OA\Property(
 *       property="due_date",
 *       type="date",
 *       description="The task due date."
 *     ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     description="The task description."
 *   ),
 *   @OA\Property(
 *      property="priority",
 *      type="date",
 *      description="The task resource priority."
 *    ),
 *   @OA\Property(
 *      property="status",
 *      type="date",
 *      description="The resource task status."
 *    ),
 * }
 */
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'due_date' => $this->due_date,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'activityId' => $this->activity_id,
            'uuid' => $this->uuid,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,

        ];
    }
}
