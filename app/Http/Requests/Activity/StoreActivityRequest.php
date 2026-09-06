<?php

namespace App\Http\Requests\Activity;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'category'         => 'required|in:art,music,outdoor,reading,motor_skills,sensory,cognitive,social,language,math,science,other',
            'description'      => 'nullable|string|max:2000',
            'materials_needed' => 'nullable|string|max:255',
            'duration_minutes' => 'nullable|integer|min:1|max:480',
            'is_active'        => 'nullable|boolean',
        ];
    }
}
