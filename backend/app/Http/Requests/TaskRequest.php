<?php

namespace App\Http\Requests;

use App\Enums\Priority;
use App\Enums\TrilioStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * @OA\Schema(schema="TaskRequest")
 * {
 *
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="The task name"
 *   ),
 *   @OA\Property(
 *      property="status",
 *      type="string",
 *      description="The task status"
 *    ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     description="The task description"
 *   ),
 *   @OA\Property(
 *       property="due_date",
 *       type="string",
 *       description="The task start_date"
 *     )
 * }
 */
class TaskRequest extends FormRequest
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
            'due_date' => 'nullable|date_format:Y-m-d H:i:s',
            'status' => ['nullable', new Enum(TrilioStatus::class)],
            'priority' => ['nullable', new Enum(Priority::class)],
        ];
    }
}
