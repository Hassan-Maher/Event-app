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

            'price'          => ['required_without:options','nullable' , 'nullable' , 'numeric' , 'min:1'],

            'city_id'        => ['required', 'exists:cities,id'],
            'available_days' => ['required', 'array'],

            'available_from' => ['required', 'date_format:g:i A'],
            'available_to'   => ['required', 'date_format:g:i A'],


            
            'options'         => [ 'nullable' ,'array' , 'min:2'],
            'options.*.name'  => [ 'required_with:options.*.price','string' , 'max:255'],
            'options.*.price' => [ 'required_with:options.*.name','numeric' , 'min:1']



        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $price = $this->input('price');
            $options = $this->input('options', []);

            $hasOptionPrice = collect($options)->contains(function ($opt) {
                return !empty($opt['price']);
            });

            if ($price && $hasOptionPrice) {
                $validator->errors()->add('price', 'لا يمكن إدخال سعر للمنتج وفي نفس الوقت أسعار للخيارات.');
            }
        });
    }


    
}
