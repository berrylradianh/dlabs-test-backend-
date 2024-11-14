<?php

namespace App\Traits;

use App\Helpers\ValidationHelper;
use Illuminate\Contracts\Validation\Validator;

trait ValidationTrait
{
    protected function failedValidation(Validator $validator)
    {
        ValidationHelper::validationErrorResponse($validator);
    }
}
