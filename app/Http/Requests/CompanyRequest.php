<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        if($this->is('api/*'))
        {
            $response =  ApiResponse::sendResponse(422 , 'Validation Errors' , $validator->messages()->all());
            throw new ValidationException($validator , $response);
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
            
        return [
            'name'              => ['required', 'string', 'max:255'],
            'logo'              => ['required', 'image', 'mimes:jpg,jpeg,png|max:2048'],
            'commercial_number' => 'required|string|max:50|unique:stores,commercial_number',
            'latitude'         => ['required', 'numeric', 'between:-180,180'],
            'longitude'         => ['required', 'numeric', 'between:-180,180'],
            'city_id'           => ['required', 'exists:cities,id'],
        ];
    }
}
