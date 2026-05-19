<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGraduationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Resolves the route-bound Graduation model (via its UUID) and passes it to the Policy
        return $this->user()?->can('update', $this->route('graduation')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'ceremony_date' => ['required', 'date'], // Permissive: No after:today restriction here
            'fee' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'status' => ['required', 'in:draft,open,closed'],
        ];
    }
}