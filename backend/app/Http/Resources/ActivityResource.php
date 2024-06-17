<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="ActivityResource")
 * {
 *
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     description="The activity Id."
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="The role name."
 *   ),
 *   @OA\Property(
 *     property="uuid",
 *     type="string",
 *     description="The activity uuid."
 *   ),
 *  @OA\Property(
 *      property="projectId",
 *      type="string",
 *      description="The activity projectId."
 *    ),
 *   @OA\Property(
 *       property="slug",
 *       type="string",
 *       description="The activity slug to support S.E.O."
 *     ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     description="The activity description."
 *   ),
 *   @OA\Property(
 *      property="start_date",
 *      type="date",
 *      description="The role start_date."
 *    ),
 *   @OA\Property(
 *      property="end_date",
 *      type="date",
 *      description="The activity end_date."
 *    ),
 * }
 */
class ActivityResource extends JsonResource
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
            'uuid' => $this->uuid,
            'status' => $this->status->value,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'projectId' => $this->project_id,
        ];
    }
}
