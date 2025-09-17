<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProductRequest extends FormRequest
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
            'main_image'     => ['required', 'image', 'mimes:jpg,jpeg,png'],
            'extra_images'   => 'nullable|array',
            'extra_images.*' => 'image|mimes:jpg,jpeg,png',

            'service_id'     => ['required', 'exists:services,id'],
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],

            'price'          => ['required_without_all:first_price,second_price,third_price' , 'nullable' , 'numeric' , 'min:1'],

            'city_id'        => ['required', 'exists:cities,id'],
            'available_days' => ['required', 'array'],

            'available_from' => ['required', 'date_format:g:i A'],
            'available_to'   => ['required', 'date_format:g:i A'],


            
            'first_option'   => ['required_with:second_option','required_with:third_option' ,'required_with:first_price', 'nullable', 'string', 'max:255'],
            'first_price'    => ['required_without_all:price,second_price,third_price', 'required_with:first_option','nullable','numeric','min:1'],

            'second_option'  => ['required_with:third_option' ,'required_with:second_price', 'nullable', 'string', 'max:255'],
            'second_price'   => ['required_without_all:price,first_price,third_price','required_with:second_option','nullable','numeric','min:1'],

            'third_option'   => ['required_with:third_price','nullable', 'string', 'max:255'],
            'third_price'    => ['required_without_all:price,first_price,second_price','required_with:third_option','nullable','numeric','min:1'],



        ];

    }
}
