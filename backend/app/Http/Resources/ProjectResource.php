<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="ProjectResource")
 * {
 *
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     description="The project Id."
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="The role name."
 *   ),
 *   @OA\Property(
 *     property="uuid",
 *     type="string",
 *     description="The project uuid."
 *   ),
 *  @OA\Property(
 *      property="ownerId",
 *      type="string",
 *      description="The project ownerId."
 *    ),
 *   @OA\Property(
 *       property="slug",
 *       type="string",
 *       description="The project slug to support S.E.O."
 *     ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     description="The project description."
 *   ),
 * }
 */
class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'ownerId' => $this->user_id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
