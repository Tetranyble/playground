<?php

namespace App\Http\Requests;

use App\Rules\ImageOrUrl;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="MediaRequest")
 * {
 *
 *   @OA\Property(
 *       property="media",
 *       type="file",
 *       description="The media file"
 *   ),
 * }
 */
class MediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'media' => ['required', new ImageOrUrl],
        ];
    }
}
