<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="ResumeRequest")
 * {
 *
 *   @OA\Property(
 *       property="resume",
 *       type="file",
 *       description="The user resume"
 *   ),
 * }
 */
class ResumeRequest extends FormRequest
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
            'resume' => 'required|mimes:pdf|max:10240',
        ];
    }
}
