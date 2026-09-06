<?php

namespace App\Http\Requests\ActivityOccurrence;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityOccurrenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activity_id'     => 'required|exists:activities,id',
            'program_id'      => 'required|exists:programs,id',
            'staff_id'        => 'required|exists:staff,id',
            'occurrence_date' => 'required|date',
            'start_time'      => 'nullable|date_format:H:i,H:i:s',
            'end_time'        => 'nullable|date_format:H:i,H:i:s',
            'status'          => 'required|in:planned,completed,partial,cancelled',
            'materials_used'  => 'nullable|string|max:255',
            'observations'    => 'nullable|string|max:3000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input('start_time');
            $end = $this->input('end_time');

            if ($start && $end && $end < $start) {
                $validator->errors()->add('end_time', 'End time cannot be earlier than start time.');
            }
        });
    }
}
