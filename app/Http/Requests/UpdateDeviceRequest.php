<?php

namespace App\Http\Requests;

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
}
