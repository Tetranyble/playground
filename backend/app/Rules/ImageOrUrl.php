<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ImageOrUrl implements Rule
{
    public function passes($attribute, $value): bool|int
    {
        // Check if the value is a valid URL with image extension
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $value);
        }

        // Check if the value is a valid uploaded file
        if (request()->hasFile($attribute)) {
            $file = request()->file($attribute);

            return $file->isValid() && in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif']);
        }

        return false;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid image URL or an uploaded image file.';
    }
}
