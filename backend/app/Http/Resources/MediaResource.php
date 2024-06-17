<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="MediaResource")
 * {
 *
 *   @OA\Property(
 *    property="path",
 *    type="string",
 *    description="The resource link."
 *   ),
 *   @OA\Property(
 *    property="description",
 *    type="string",
 *    description="The resource description."
 *   ),
 *   @OA\Property(
 *    property="attribution",
 *    type="string",
 *    description="The resource attribution."
 *   ),
 *   @OA\Property(
 *    property="mime_type",
 *    type="string",
 *    description="The resource mime type. RFC 6838."
 *   ),
 *   @OA\Property(
 *    property="size",
 *    type="string",
 *    description="The resource file size."
 *   ),
 *   @OA\Property(
 *    property="current",
 *    type="boolean",
 *    description="The resource is default."
 *   ),
 * }
 */
class MediaResource extends JsonResource
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
            'mime_type' => $this->mime_type,
            'description' => $this->description,
            'attribution' => $this->attribution,
            'path' => $this->url,
            'size' => $this->size,
            'current' => $this->current,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
