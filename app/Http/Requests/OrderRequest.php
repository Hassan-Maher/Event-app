<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductOption;

use function PHPUnit\Framework\isEmpty;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OrderRequest extends FormRequest
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
            'price' => [ 'required', 'numeric' , 'min:1'],
            'offer' =>  [ 'nullable', 'numeric' , 'min:1' , 'max:100'],
            'payment_method' => ['required' , 'string'],
            'customer_name' =>  ['nullable',  'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20' ,'regex:/^\+\d{1,4}[0-9]{7,12}$/'],
            'latitude'         => ['required', 'numeric', 'between:-90,90'],
            'longitude'         => ['required', 'numeric', 'between:-180,180'],
            'items' => ['required' , 'array' , 'min:1'],
            'items.*.item_id'  => 'required',
            'items.*.quantity' => ['required' , 'min:1' , 'integer'],
            'items.*.option_id' => 'nullable|integer'
            
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

                    $product =Product::find($item['item_id']);
                    $package = Package::find($item['item_id']);

                    if (!$product && !$package) {
                        $validator->errors()->add("items.$index.item_id", 'Item must exist in either products or packages.');
                    }

                    if ($product) {
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
                                $validator->errors()->add("items.$index.option",'This option does not belong to this product.');
                            }
                        }
                    }

                    if ($package) {
                        if (!empty($item['option'])) {
                            $validator->errors()->add("items.$index.option", 'Option is not allowed for packages.');
                        }
                    }
                }
            });
        }

    

}