<?php

namespace App\Http\Requests\Academics;

use Illuminate\Validation\Rule;
use App\Helpers\Enums\GenderType;
use App\Helpers\Enums\ReligionType;
use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
      'nim' => [
        'required', 'numeric',
        Rule::unique('students', 'nim')->ignore($this->student),
      ],
      'nik' => [
        'nullable', 'numeric', 'digits:16',
        Rule::unique('students', 'nik')->ignore($this->student),
      ],
      'name' => 'required|string|max:100',
      'email' => [
        'nullable', 'email:dns', 'max:100',
        Rule::unique('students', 'email')->ignore($this->student),
      ],
      'birth_place' => 'required|string|max:50',
      'birth_date' => 'required|date',
      'gender' => "required|string|" . GenderType::toValidation(),
      'religion' => "nullable|string|" . ReligionType::toValidation(),
      'phone' => [
        'required', 'numeric',
        Rule::unique('students', 'phone')->ignore($this->student),
      ],
      'major_id' => 'required|exists:majors,id',
      'province' => 'required|exists:provinces,id',
      'regency' => 'required|exists:regencies,id',
      'district' => 'required|exists:districts,id',
      'village' => 'required|exists:villages,id',
      'post_code' => 'required|numeric',
      'address' => 'required|string',
      'file' => 'nullable|mimes:jpg,png|max:3048',
      'initial_registration_period' => 'nullable|string',
      'origin_department' => 'nullable|string',
      'parent_name' => 'nullable|string|max:100',
      'parent_phone_number' => 'nullable|numeric',
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   */
  public function messages(): array
  {
    return [
      '*.required' => ':attribute harus tidak boleh dikosongkan',
      '*.min' => ':attribute maksimal :min karakter',
      '*.in' => ':attribute harus salah satu dari jenis berikut: :values',
      '*.unique' => ':attribute sudah digunakan, silahkan pilih yang lain',
      '*.exists' => ':attribute tidak ditemukan atau tidak bisa diubah',
      '*.numeric' => ':attribute input tidak valid atau harus berupa angka',
      '*.image' => ':attribute tidak valid, pastikan memilih gambar',
      '*.mimes' => ':attribute tidak valid, masukkan gambar dengan format jpg atau png',
      '*.max' => ':attribute terlalu besar, maksimal :max karakter',
      '*.date' => ':attribute harus berupa tanggal',
      '*.digits' => ':attribute harus memiliki :digits angka',
      '*.between' => ':attribute harus berada diantara tahun :min sampai :max',
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
      'nim' => 'Nomor Induk Mahasiswa',
      'nik' => 'Nomor Induk Kependudukan',
      'name' => 'Nama Lengkap',
      'email' => 'Email',
      'birth_place' => 'Tempat Lahir',
      'birth_day' => 'Tanggal Lahir',
      'gender' => 'Jenis Kelamin',
      'religion' => 'Agama',
      'phone' => 'No. Whatsapp',
      'major_id' => 'Jurusan',
      'province' => 'Provinsi',
      'regency' => 'Kabupaten',
      'district' => 'Kecamatan',
      'village' => 'Kelurahan',
      'post_code' => 'Kode Pos',
      'address' => 'Alamat Lengkap',
      'file' => 'Pas Foto',
      'initial_registration_period' => 'Tahun Masuk',
      'parent_name' => 'Nama Orang Tua',
      'parent_phone_number' => 'No. Telepon Orang Tua',
    ];
  }
}
