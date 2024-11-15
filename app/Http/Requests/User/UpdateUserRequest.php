<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string',
            'email' => 'sometimes|email',
            'age' => 'sometimes|integer',
            'password' => 'sometimes|string'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (DB::table('users')->where('email', $this->email)->exists()) {
                $validator->errors()->add('email', 'The email has already been taken.');
            }
        });
    }
}
