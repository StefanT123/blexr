<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkFromHomeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date_format:d-m-Y',
            'hours' => 'required|integer',
        ];
    }
}
