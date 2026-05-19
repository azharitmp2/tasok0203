<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('student')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Fetch the underlying database ID of the route-bound student
        $studentId = $this->route('student')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'ic' => [
                'required',
                'string',
                'size:12',
                Rule::unique('students', 'ic')->ignore($studentId), // Ignore self on edit
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('students', 'email')->ignore($studentId), // Ignore self on edit
            ],
            'matric_card' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'payment_receipt' => [
                'nullable', // Optional so info updates can occur without forcing re-upload
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048', // Maximum file size cap: 2MB
            ],
        ];
    }
}