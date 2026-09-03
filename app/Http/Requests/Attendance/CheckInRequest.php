<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'child_id'        => 'required|exists:children,id',
            'program_id'      => 'required|exists:programs,id',
            'attendance_date' => 'required|date',
            'check_in_time'   => 'nullable|date_format:H:i,H:i:s',
            'status'          => 'nullable|in:present,late',
            'notes'           => 'nullable|string|max:500',
        ];
    }
}
