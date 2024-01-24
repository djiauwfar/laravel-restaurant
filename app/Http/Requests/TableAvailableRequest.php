<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TableAvailableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function all($keys = null)
	{
	  $request = parent::all($keys);
	  $request['date'] = $this->input("date");

	  return $request;
	}

    public function rules(): array
    {
        return [
            "date" => ["nullable", "date_format:Y-m-d H:i:s"],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'error' => $validator->getMessageBag()
        ], 400));
    }
}
