<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class BulkAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'child_ids'       => 'nullable|array',
            'child_ids.*'     => 'exists:children,id',
            'attendance_ids'  => 'nullable|array',
            'attendance_ids.*'=> 'exists:attendance,id',
            'program_id'      => 'nullable|exists:programs,id',
            'attendance_date' => 'required|date',
            'action'          => 'required|in:check_in,check_out,mark_absent',
            'time'            => 'nullable|date_format:H:i,H:i:s',
            'notes'           => 'nullable|string|max:500',
        ];
    }
}
