<?php

namespace App\Http\Requests\DailyLog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChildDailyLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id'               => 'nullable|exists:staff,id',
            'activity_occurrence_id' => 'nullable|exists:activity_occurrences,id',
            'log_date'               => 'required|date',
            'log_type'               => 'required|in:nap,meal,bottle,diaper_change,activity,incident,special_program,medication,other',
            'start_time'             => 'nullable|date_format:H:i,H:i:s',
            'end_time'               => 'nullable|date_format:H:i,H:i:s',
            'meal_type'              => 'nullable|required_if:log_type,meal|in:breakfast,lunch,snack,bottle',
            'items_served'           => 'nullable|string|max:255',
            'amount_eaten'           => 'nullable|string|max:255',
            'quality'                => 'nullable|in:good,fair,poor,refused',
            'is_completed'           => 'nullable|boolean',
            'notes'                  => 'nullable|string|max:2000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input('start_time');
            $end = $this->input('end_time');
            $type = $this->input('log_type');
            $notes = $this->input('notes');

            if ($start && $end && $end < $start) {
                $validator->errors()->add('end_time', 'End time cannot be earlier than start time.');
            }

            if ($type === 'incident' && empty(trim((string)$notes))) {
                $validator->errors()->add('notes', 'Notes detailing the incident are required.');
            }
        });
    }
}
