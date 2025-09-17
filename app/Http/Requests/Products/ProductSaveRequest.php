<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductSaveRequest extends FormRequest {

    public function authorize() {
        return true;
    }

    /**
     * Validation используется как при создании так и при обновлении user
     * @return string[]
     */
    public function rules() {
        $rules = [
            'category' => [
                'required',
                'string',
                'max:255',
                'in:' . implode(',', config('site.products.categories')),
            ],
            'sub_category' => [
                'required',
                'string',
                'max:255',
                'in:' . implode(',', config('site.products.sub_categories')),
            ],
            'price_per_item' => 'required|numeric|min:0',
            'main_last_price' => 'required|numeric|min:0',
            'latest_price' => 'required|numeric|min:0',
            'container_deposit' => 'required|numeric|min:0',
            'ean' => 'required|string|max:255',
            'item_plan_bu_grp' => 'required|string|max:255',
            'locally_owned' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'provider_item_ean' => 'required|string|max:255',
            'provider_item_name' => 'required|string|max:255',
            'provider_item_pack_qty' => 'required|integer|min:0',
            'provider_item_vendor_code' => 'required|string|max:255',
            'provider_name' => 'required|string|max:255',
            'selling_type' => 'required|string|max:255',
            'tax' => 'required|numeric|min:0',
            'vendor_code' => 'required|string|max:255',
        ];

        return $rules;
    }

}
