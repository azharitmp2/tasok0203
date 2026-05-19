<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Graduation;
use Illuminate\Foundation\Http\FormRequest;

class StoreGraduationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Indirectly checks GraduationPolicy::create()
        return $this->user()?->can('create', Graduation::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'ceremony_date' => ['required', 'date', 'after:today'], // Mandatory: Can't backdate a new event
            'fee' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'status' => ['required', 'in:draft,open,closed'],
        ];
    }
}