<?php

namespace App\Http\Requests;

use App\Constant\TableConstant;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{

    public function prepareForValidation()
    {
        $this->merge([
            'categoryId'=>$this->route('category',null)
        ]);
    }
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image_file' => 'nullable|image|max:2048|mimes:png,jpeg,svg,jpg',
            'name' => ['required','string',Rule::unique(TableConstant::CATEGORY,'name')->ignore($this->categoryId)]
        ];
    }
}
