<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookOnlineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        return [
            "table_code" => ["nullable"],
            "user_id" => ["nullable"],
            "date" => ["nullable", "date_format:Y-m-d H:i:s"],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "error" => $validator->getMessageBag()
        ], 400));
    }
}
