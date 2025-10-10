<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductOption;
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
            'name'                    => ['required' , 'string' , 'max:255'],
            'description'             => ['nullable' , 'string'],
            'image'                   => ['required' , 'image' , 'mimes:jpg,jpeg,png'],
            'price'                   => ['required' , 'numeric', 'min:1'],
            'offer'                   => ['nullable' , 'min:1' , 'numeric'],
            'duration'                => ['required' , 'integer' , 'min:1'],
            'products'                => ['required', 'array', 'min:2'],
            'products.*.id'           => ['integer', 'exists:products,id' , Rule::in($productsIds)],
            'products.*.option_id'    => [ 'nullable', 'integer', 'exists:product_options,id'],

        ];

    }
        public function withValidator($validator)
        {
            $validator->after(function ($validator) {

 
                if(empty($this->products))
               {
                    return;
               }
                foreach ($this->products as $index => $product) 
                {
                    $test_product =Product::find($product['id']);
                    if (! $test_product) {
                        continue; 
                    }

                        $options = $test_product->options;

                        if ($options->isNotEmpty() && empty($product['option_id'])) 
                        {
                            $validator->errors()->add("products.$index.option_id", 'This product requires an option.');
                        } 
                        elseif (! empty($product['option_id'])) 
                        {
                            if($options->isEmpty())
                            {
                                $validator->errors()->add("products.$index.option_id",'This product does not contain options.');
                            } 
                            else if (!$options->where('id', $product['option_id'])->count()) 
                            {
                                $validator->errors()->add("products.$index.option_id",'This option does not belong to this product.');
                            }
                        }
                    
                }
            });
        }
    
    
}
