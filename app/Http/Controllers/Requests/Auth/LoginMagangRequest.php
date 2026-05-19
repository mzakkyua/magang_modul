<?php

namespace App\Http\Controllers\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ======================================================================
 * FORM REQUEST: LoginMagangRequest
 * ======================================================================
 *
 * Memindahkan logika validasi login dari AuthMagangController
 * agar controller lebih ringkas.
 *
 * Normalisasi email juga dilakukan di sini via prepareForValidation()
 * sehingga tidak perlu strtolower() manual di controller.
 *
 * ======================================================================
 */
class LoginMagangRequest extends FormRequest
{

  public function authorize(): bool
  {
    return true;
  }


  /**
   * Normalisasi email sebelum validasi dijalankan.
   */
  protected function prepareForValidation(): void
  {
    if ($this->has('email')) {
      $this->merge([
        'email' => strtolower(trim($this->email)),
      ]);
    }
  }


  public function rules(): array
  {
    return [
      'email'    => ['required', 'email'],
      'password' => ['required', 'string'],
      'remember' => ['nullable', 'boolean'],
    ];
  }


  public function messages(): array
  {
    return [
      'email.required'    => 'Email wajib diisi.',
      'email.email'       => 'Format email tidak valid.',
      'password.required' => 'Password wajib diisi.',
    ];
  }
}
