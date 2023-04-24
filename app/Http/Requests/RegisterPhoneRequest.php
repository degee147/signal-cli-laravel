<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterPhoneRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'captcha' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'captcha.required' => 'The captcha field is required. Generate Captcha from https://signalcaptchas.org/registration/generate.html or from https://signalcaptchas.org/challenge/generate.html',
        ];
    }
}
