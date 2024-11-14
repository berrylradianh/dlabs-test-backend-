<?php

namespace App\Http\Requests\Pagination;

use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'size' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get the custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'The page parameter must be a valid integer.',
            'page.min' => 'The page must be at least 1.',
            'size.integer' => 'The size parameter must be a valid integer.',
            'size.min' => 'The size must be at least 1.',
        ];
    }

    /**
     * Return the pagination parameters with default values.
     *
     * @return array
     */
    public function paginationParams(): array
    {
        return [
            'page' => $this->input('page', 1),
            'size' => $this->input('size', 10),
        ];
    }
}
