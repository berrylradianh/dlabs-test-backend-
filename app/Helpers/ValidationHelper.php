<?php

namespace App\Helpers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidationHelper
{
    public static function validationErrorResponse(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $response = response()->json([
            'success' => false,
            'message' => 'Validation errors occurred.',
            'errors' => $errors
        ], 422);

        throw new HttpResponseException($response);
    }
}
