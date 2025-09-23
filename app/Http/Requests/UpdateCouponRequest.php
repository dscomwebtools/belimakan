<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Coupon;

class UpdateCouponRequest extends FormRequest
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
            'code'              => ['required', 'string', 'max:50', Rule::unique(Coupon::class)->ignore($this->coupon)],
            'description'       => ['nullable', 'string'],
            'min_amount'        => ['nullable', 'numeric', 'min:0'],
            'discount_amount'   => ['required', 'numeric', 'min:0'],
            'discount_percent'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'valid_from'        => ['nullable', 'date'],
            'valid_until'       => ['nullable', 'date', 'after_or_equal:valid_from'],
            'max_uses'          => ['nullable', 'integer', 'min:1'],
            'max_uses_per_user' => ['nullable', 'integer', 'min:1'],
            'active'            => ['boolean'],
        ];
    }
}
