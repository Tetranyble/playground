<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="ProjectRequest")
 * {
 *
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="The project name"
 *   ),
 *   @OA\Property(
 *      property="status",
 *      type="string",
 *      description="The project status"
 *    ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     description="The project description"
 *   )
 * }
 */
class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:10000',
            'status' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
