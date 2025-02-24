<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MeetingRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'datetime'    => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'link'        => 'nullable|string',
            'name'        => 'required|string|max:255',
            'start_time'  => 'nullable|date_format:Y-m-d\TH:i:s.v',
            'end_time'    => 'nullable|date_format:Y-m-d\TH:i:s.v|after_or_equal:start_time',
            'description' => 'nullable|string',
            'type'        => 'required|in:remotely,normal',
            'status'      => 'nullable|in:public,private',
        ];
    }

    public function messages()
    {
        return [
            'name.required'       => 'اسم الاجتماع مطلوب.',
            'name.max'            => 'يجب ألا يزيد اسم الاجتماع عن 255 حرفًا.',
            'datetime.date_format'=> 'صيغة التاريخ غير صحيحة.',
            'start_time.date_format' => 'صيغة وقت البدء غير صحيحة.',
            'end_time.date_format'   => 'صيغة وقت الانتهاء غير صحيحة.',
            'end_time.after_or_equal' => 'يجب أن يكون وقت الانتهاء بعد أو يساوي وقت البدء.',
            'type.required'       => 'نوع الاجتماع مطلوب.',
            'type.in'             => 'نوع الاجتماع يجب أن يكون إما remotely أو normal.',
            'status.in'           => 'حالة الاجتماع يجب أن تكون إما public أو private.',
        ];
    }
}
