<?php

namespace App\Http\Requests;

use App\Enums\TrilioStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * @OA\Schema(schema="ActivityRequest")
 * {
 *
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="The activity name"
 *   ),
 *   @OA\Property(
 *      property="status",
 *      type="string",
 *      description="The activity status"
 *    ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     description="The activity description"
 *   ),
 *   @OA\Property(
 *       property="start_date",
 *       type="string",
 *       description="The activity start_date"
 *     ),
 *   @OA\Property(
 *      property="end_date",
 *      type="string",
 *      description="The activity end_date"
 *    )
 * }
 */
class ActivityRequest extends FormRequest
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
            'start_date' => 'nullable|date_format:Y-m-d H:i:s',
            'end_date' => 'nullable|date_format:Y-m-d H:i:s|after:start_date',
            'status' => ['nullable', new Enum(TrilioStatus::class)]
        ];
    }
}
