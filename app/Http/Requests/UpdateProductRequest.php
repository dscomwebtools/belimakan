<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', Rule::unique(Product::class)->ignore($this->product)],
            'stock' => ['nullable', 'numeric'],
            'price' => ['nullable', 'numeric'],
            'weight' => ['nullable', 'numeric'],
            'sku' => ['nullable', 'string', Rule::unique(ProductVariant::class)->ignore($this->product->product_variant[0])],
            'active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'categories' => ['nullable'],
            'product_pictures' => ['nullable'],
            'deleted_pictures' => ['nullable'],
            'picture_positions' => ['nullable', 'string'],
            'discount' => ['nullable'],
            'length' => ['nullable'],
            'width' => ['nullable'],
            'height' => ['nullable'],
            'detail' => ['nullable'],
            'detail-value' => ['nullable'],
            'variantData' => ['nullable'],
            'variantCombinationsData' => ['nullable'],
            'variant_pictures' => ['nullable', 'array'],
            'variant_pictures.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'deleted_variant_pictures' => ['nullable', 'array'],
            'deleted_variant_pictures.*' => ['string'],
            'stock_variant' => 'nullable',
            'price_variant' => 'nullable',
            'weight_variant' => 'nullable',
            'sku_variant' => 'nullable',
            'active_variant' => 'nullable',
            'brand_id' => 'nullable|exists:brands,id',
            'youtube' => 'nullable',
            'seo_title' => 'nullable',
            'seo_description' => 'nullable',
            'pre_order' => 'required|boolean',
            'pre_order_date' => 'nullable|required_if:pre_order,1|date',
            'pre_order_message' => 'nullable|required_if:pre_order,1|string|max:1000',
            'call_to_buy' => 'required|boolean',
        ];
    }

    function messages()
    {
        return [
            'name.unique' => 'Nama produk ini sudah ada.'
        ];
    }
}
