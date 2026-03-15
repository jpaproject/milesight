<?php

namespace App\Http\Requests;

use App\Models\Device;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
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
        $deviceId = $this->route('device');

        return [
            'area_id'   => 'required|exists:areas,id',
            'name'      => 'required|string|max:255|unique:devices,name,' . $deviceId,
            'is_active' => 'required|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('area_id')) {
            return;
        }

        $deviceId = $this->route('device');
        if (!$deviceId) {
            return;
        }

        $currentAreaId = Device::query()->whereKey($deviceId)->value('area_id');
        if ($currentAreaId) {
            $this->merge(['area_id' => $currentAreaId]);
        }
    }

    public function messages(): array
    {
        return [
            'area_id.required' => 'Konfigurasi default belum tersedia. Pastikan data dasar sudah dibuat.',
            'area_id.exists' => 'Konfigurasi default tidak valid. Pastikan data dasar sudah benar.',
        ];
    }
}
