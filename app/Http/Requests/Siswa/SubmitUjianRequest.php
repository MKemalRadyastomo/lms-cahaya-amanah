<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;

class SubmitUjianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jawaban' => ['nullable', 'array'],
            'jawaban.*' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
