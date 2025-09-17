<?php

namespace App\Http\Requests\PromotionSurfacesDesign;

use Illuminate\Foundation\Http\FormRequest;

class SetFilesBriefDesignRequest extends FormRequest {

    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        // Получение расширений из конфигурации
        $extensions = config('site.files');
        // Объединение разрешенных расширений
        $allowedExtensions = array_merge($extensions['extensions_image'], $extensions['extensions_document']);
        $allowedExtensionsRegex = implode('|', $allowedExtensions);

        return [
            'promotion_surface_design_id' => ['required', 'integer', 'exists:promotion_surface_designs,id'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => [ 'required', 'string' ],
            'names' => ['required', 'array'],
            'names.*' => [
                'required',
                'string',
                'max:255',
                'regex:/\.(' . $allowedExtensionsRegex . ')$/i' // Проверка на расширение
            ],
            'sizes' => ['required', 'array'],
            'sizes.*' => ['required', 'numeric', 'min:0.00'],
            'dates' => ['required', 'array'],
            'dates.*' => ['required', 'date']
        ];
    }

}
