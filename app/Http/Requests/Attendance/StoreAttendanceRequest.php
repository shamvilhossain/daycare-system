<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            'status'          => 'required|in:present,absent,late,excused',
            'check_in_time'   => 'nullable|date_format:H:i,H:i:s',
            'check_out_time'  => 'nullable|date_format:H:i,H:i:s',
            'notes'           => 'nullable|string|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $checkIn = $this->input('check_in_time');
            $checkOut = $this->input('check_out_time');
            $status = $this->input('status');

            if ($checkIn && $checkOut && $checkOut <= $checkIn) {
                $validator->errors()->add('check_out_time', 'Check-out time must be after check-in time.');
            }

            if (in_array($status, ['absent', 'excused']) && ($checkIn || $checkOut)) {
                $validator->errors()->add('status', 'A child marked as absent or excused cannot have check-in or check-out times.');
            }
        });
    }
}
