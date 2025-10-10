<?php

namespace App\Http\Requests;

use App\Models\Package;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class EditItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_id'   => ['required' , 'integer'],
            'type'      => ['required' , 'in:product,package'],
            'option_id' => ['nullable' , 'exists:Product_options,id'],
            'quantity'  => ['required' , 'integer' , 'min:1']
        ];

    }
        public function withValidator($validator)
        {
            $validator->after(function ($validator) {

                if ($validator->errors()->any()) {
                    return; 
                }
                
                    if($this->type == 'product')
                    {
                        $product =Product::find($this->item_id);

                        if(!$product)
                        {
                            $validator->errors()->add('invalid product id.');
                        }

                        if ($product) 
                        {
                            if (! in_array(strtolower(now()->format('l')), $product->available_days)) 
                            {
                                $validator->errors()->add('item' , 'product is not available on this day');
                            }

                            if (! now()->between($product->available_from, $product->available_to)) 
                            {
                                $validator->errors()->add('item' , 'product is not available at this time');
                            }

                            $options = $product->options;
                            if ($options->isNotEmpty() && empty($this->option_id)) {

                                $validator->errors()->add( 'item','This product requires an option.');
                            } 
                            elseif (!empty($this->option_id)) {
                                
                                if ($options->isEmpty()) {
                                    $validator->errors()->add( 'item','This product does not contain options.');
                                } 
                                elseif (!$options->where('id', $this->option_id)->count()) {
                                    $validator->errors()->add( 'item','This option does not belong to this product.');
                                }
                            }
                        }
                    }

                    if($this->type == 'package')
                    {
                        $package = Package::find($this->item_id);

                        if (!$package) 
                        {
                            $validator->errors()->add( 'item', 'invalid package id.');
                        }

                        if ($package)
                        {
                            if (!empty($this->option_id)) {
                                $validator->errors()->add('item' ,'Option is not allowed for packages.');
                            }

                            if($package->end_date < now())
                            {
                                $validator->errors()->add('item', 'package is not available ');
                            }
                        }
                    }


                    
                    
                }
            );
        }

    
}
