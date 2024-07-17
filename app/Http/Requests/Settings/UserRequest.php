<?php

namespace App\Http\Requests\Settings;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
    $validation = [
      'name' => 'required|string|max:50',
      'email' => [
        'required', 'email:dns',
        Rule::unique('users', 'email')->ignore($this->user),
      ],
      'file' => 'nullable|mimes:jpg,png|max:3048',
      'roles' => 'required|exists:roles,id',
      'phone' => 'nullable|numeric',
    ];

    return $validation;
  }

  /**
   * Get the error messages for the defined validation rules.
   */
  public function messages(): array
  {
    return [
      '*.required' => ':attribute harus tidak boleh dikosongkan',
      '*.max' => ':attribute maksimal :max karakter',
      '*.min' => ':attribute maksimal :min karakter',
      '*.in' => ':attribute harus salah satu dari jenis berikut: :values',
      '*.unique' => ':attribute sudah digunakan, silahkan pilih yang lain',
      '*.exists' => ':attribute tidak ditemukan atau tidak bisa diubah',
      '*.numeric' => ':attribute input tidak valid atau harus berupa angka',
      '*.image' => ':attribute tidak valid, pastikan memilih gambar',
      '*.mimes' => ':attribute tidak valid, masukkan gambar dengan format jpg atau png',
      '*.max' => ':attribute terlalu besar, maksimal :max kb',
    ];
  }

  /**
   * Get the validation attribute names that apply to the request.
   *
   * @return array<string, string>
   */
  public function attributes(): array
  {
    return [
      'name' => 'Nama',
      'email' => 'Email',
      'file' => 'Avatar',
      'phone' => 'Telepon'
    ];
  }
}
