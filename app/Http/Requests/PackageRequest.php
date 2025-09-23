<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PackageRequest extends FormRequest
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
        $productsIds = $this->user()->store->product->pluck('id')->toArray();

        return [
            'name'          => ['required' , 'string' , 'max:255'],
            'description'   => ['nullable' , 'string'],
            'image'         => ['required' , 'image' , 'mimes:jpg,jpeg,png'],
            'price'         => ['required' , 'numeric', 'min:1'],
            'offer'         => ['nullable' , 'min:1' , 'numeric'],
            'duration'      => ['required' , 'string'],
            'products'      => ['required', 'array', 'min:2'],
            'products.*'    => ['integer', 'exists:products,id' , Rule::in($productsIds)],
        ];
    }
}
