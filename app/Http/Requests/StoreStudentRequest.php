<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Student::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'ic' => ['required', 'string', 'size:12', 'unique:students,ic'], // 12-digit Malaysian IC format
            'email' => ['required', 'email', 'unique:students,email'],
            'matric_card' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
        ];
    }
}