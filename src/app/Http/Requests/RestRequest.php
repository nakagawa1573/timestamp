<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'work_id' => 'exists:works,id',
            'id' => 'exists:rests,id',
        ];
    }

    public function messages()
    {
        return[
            'work_id.exists' => 'まだ勤務が開始されていません',
            'id.exists' => 'まだ休憩が開始されていません',
        ];
    }
}
