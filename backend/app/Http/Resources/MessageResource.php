<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="MessageResource")
 * {
 *
 *   @OA\Property(
 *       property="id",
 *       type="integer",
 *       description="The user id"
 *    ),
 *   @OA\Property(
 *       property="subject",
 *       type="string",
 *       description="The resource subject"
 *    ),
 *   @OA\Property(
 *       property="content",
 *       type="string",
 *       description="The resource content"
 *    ),
 * @OA\Property(
 *        property="is_read",
 *        type="string",
 *        description="The resource read status"
 *     ),
 * @OA\Property(
 *        property="user_id",
 *        type="integer",
 *        description="The resource owner Id"
 *     ),
 *   @OA\Property(
 *       property="created_at",
 *       type="string",
 *       description="The resource created date."
 *    ),
 * @OA\Property(
 *        property="updated_at",
 *        type="string",
 *        description="The resource updated date."
 *     ),
 * }
 */
class MessageResource extends JsonResource
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
            'subject' => $this->subject,
            'content' => $this->content,
            'is_read' => $this->is_read,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
