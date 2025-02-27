<?php

namespace App\Http\Requests\Settings;

use App\Helpers\Enums\RoleType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
      'name' => [
        'required', 'string', 'max:50',
        Rule::unique('roles', 'name')->ignore($this->role),
      ],
      'permission' => [
        'nullable',
      ],
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   */
  public function messages(): array
  {
    return [
      '*.required' => ':attribute tidak boleh dikosongkan',
      '*.string' => ':attribute tidak valid, masukkan yang benar',
      '*.max' => ':attribute terlalu panjang, maksimal :max karakter',
      '*.unique' => ':attribute sudah digunakan, silahkan pilih yang lain',
      '*.in' => ':attribute tidak sesuai dengan data kami',
    ];
  }

  /**
   * Get custom attributes for validator errors.
   *
   */
  public function attributes(): array
  {
    return [
      'name' => 'Peran',
      'permission' => 'Hak Akses',
    ];
  }
}
