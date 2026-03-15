<?php

namespace App\Http\Requests;

use App\Models\Area;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDeviceRequest extends FormRequest
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
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255|unique:devices,name',
            'is_active' => 'required|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->filled('area_id')) {
            $defaultAreaId = Area::query()->orderBy('id')->value('id');
            if ($defaultAreaId) {
                $this->merge(['area_id' => $defaultAreaId]);
            }
        }
    }

    public function messages(): array
    {
        return [
            'area_id.required' => 'Konfigurasi default belum tersedia. Pastikan data dasar sudah dibuat.',
            'area_id.exists' => 'Konfigurasi default tidak valid. Pastikan data dasar sudah benar.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('show_create_modal', true)
        );
    }
}
