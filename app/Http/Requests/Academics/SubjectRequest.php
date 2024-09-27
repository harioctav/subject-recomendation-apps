<?php

namespace App\Http\Requests\Academics;

use App\Helpers\Enums\StatusSubjectType;
use App\Helpers\Enums\SubjectNoteType;
use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
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
    $statusValidation = StatusSubjectType::toValidation();

    return [
      'code' => 'required|string|max:100',
      'name' => 'required|string|max:100',
      'course_credit' => 'required|integer|min:1|max:5',
      'status' => "required|string|{$statusValidation}",
      'exam_time' => 'required|string|regex:/^\d+\.\d+$/',
      'notes' => 'nullable|array',
      'notes.*' => 'string|' . SubjectNoteType::toValidation(),
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
      '*.max' => ':attribute terlalu panjang, maksimal :max.',
      '*.min' => ':attribute terlalu panjang, maksimal :min.',
      '*.integer' => ':attribute harus berupa angka',
      '*.in' => ':attribute tidak sesuai dengan data kami',
      '*.exists' => ':attribute tidak ditemukan di storage kami',
      '*.regex' => ':attribute harus dalam format angka.angka, misalnya 1.1 atau 2.5 dst'
    ];
  }

  /**
   * Get custom attributes for validator errors.
   *
   */
  public function attributes(): array
  {
    return [
      'code' => 'Kode Matakuliah',
      'name' => 'Nama Matakuliah',
      'course_credit' => 'Jumlah SKS',
      'status' => 'Status',
      'exam_time' => 'Waktu Ujian',
      'note' => 'Keterangan',
      'notes' => 'Keterangan',
      'notes.*' => 'Pilihan Keterangan'
    ];
  }
}
