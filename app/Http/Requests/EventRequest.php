<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class EventRequest extends FormRequest
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
            'name'                  => ['required' , 'string'],
            'date'                  => ['required' , 'date_format:Y-m-d g:i A'],
            'latitude'              => ['required', 'numeric', 'between:-90,90'],
            'longitude'             => ['required', 'numeric', 'between:-180,180'],
            'number_of_guests'      => ['required' , 'integer' , 'min:1'],
            'additional_details'    => ['nullable' , 'string'],
            'images'                => ['nullable' , 'array'],
            'images.*.image'        => ['required_with:images.*.type','image , mimes:jpg,jpeg,png'],
            'images.*.type'         => ['required_with:images.*.image' , 'in:decore,food,place'],
            'items'                 => ['required' , 'array' , 'min:1'],
            'items.*.item_id'       => ['required'],
            'items.*.quantity'      => ['required' ,'integer', 'min:1'],
            'items.*.option_id'     => ['nullable']
        ];
    }

     public function withValidator($validator)
        {
            $validator->after(function ($validator) {
               if(empty($this->items))
               {
                    return;
               }
                foreach ($this->items as $index => $item) {

                    $product = Product::find($item['item_id']);
                    $package = Package::find($item['item_id']);

                    if (!$product && !$package) {
                        $validator->errors()->add("items.$index.item_id", 'Item must exist in either products or packages.');
                    }

                    if ($product) {

                        
                        if (! in_array(now()->format('l'), $product->available_days)) 
                        {
                            $validator->errors()->add("items.$index.item_id",'Item ' . $index . ' is not available on this day');
                        }

                        if (! now()->between($product->available_from, $product->available_to)) 
                        {
                            $validator->errors()->add("items.$index.item_id",'Item ' . $index . ' is not available at this time');
                        }

                        $options = $product->options;
                        if ($options->isNotEmpty() && empty($item['option_id'])) {

                            $validator->errors()->add("items.$index.option_id", 'This product requires an option.');
                        } 
                        elseif (!empty($item['option_id'])) {
                            if (! ProductOption::where('id', $item['option_id'])->exists()) {
                                $validator->errors()->add("items.$index.option_id", 'Invalid option ID.');
                            } 
                            elseif ($options->isEmpty()) {
                                $validator->errors()->add("items.$index.option_id",'This product does not contain options.');
                            } 
                            elseif (!$options->where('id', $item['option_id'])->count()) {
                                $validator->errors()->add("items.$index.option_id",'This option does not belong to this product.');
                            }
                        }
                    }

                    if ($package) {
                        if (!empty($item['option'])) {
                            $validator->errors()->add("items.$index.option", 'Option is not allowed for packages.');
                        }

                        foreach ($package->products as $pIndex => $product) {
                            if (!in_array(now()->format('l'), $product->available_days)) {
                                $validator->errors()->add("items.$index.products.$pIndex.item_id", 'Package ' . $index . ' contains a product that is not available on this day');
                            }
                            if (!now()->between($product->available_from, $product->available_to)) {
                                $validator->errors()->add("items.$index.products.$pIndex.item_id", 'Package ' . $index . ' contains a product that is not available on this time');
                            }
                        }
                    }

                }
                
            });
            
        }

    
}
