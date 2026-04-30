<?php

namespace App\Http\Controllers\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * ======================================================================
 * FORM REQUEST: RegisterMagangRequest
 * ======================================================================
 *
 * Memindahkan semua logika validasi register dari AuthMagangController
 * ke class ini agar controller lebih ringkas dan validasi bisa
 * di-reuse / di-test secara independen.
 *
 * IMPROVEMENT:
 * - Password policy diperkuat: wajib huruf besar + angka (min 8 karakter)
 * - Email dinormalisasi (strtolower) via prepareForValidation()
 *   sehingga controller tidak perlu melakukan ini secara manual.
 *
 * ======================================================================
 */
class RegisterMagangRequest extends FormRequest
{

  /**
   * Semua user yang mengakses route ini diizinkan membuat request.
   * Akses guard diatur di middleware (guest:magang).
   */
  public function authorize(): bool
  {
    return true;
  }


  /**
   * -----------------------------------------------------------------------
   * prepareForValidation()
   * -----------------------------------------------------------------------
   * Normalisasi input SEBELUM rules dijalankan.
   * Email di-lowercase agar validasi unique tidak case-sensitive
   * dan controller tidak perlu strtolower() manual.
   * -----------------------------------------------------------------------
   */
  protected function prepareForValidation(): void
  {
    if ($this->has('email')) {
      $this->merge([
        'email' => strtolower(trim($this->email)),
      ]);
    }
  }


  /**
   * -----------------------------------------------------------------------
   * rules()
   * -----------------------------------------------------------------------
   *
   * PASSWORD POLICY (diperkuat dari versi sebelumnya):
   * - min:8              → minimal 8 karakter
   * - regex huruf besar  → wajib ada minimal 1 huruf kapital
   * - regex angka        → wajib ada minimal 1 digit angka
   * - confirmed          → harus match dengan password_confirmation
   *
   * Kenapa penting?
   * Password "abcdefgh" (8 karakter huruf kecil semua) sangat mudah
   * di-brute force dengan dictionary attack. Kombinasi huruf besar
   * + angka meningkatkan entropy secara signifikan.
   * -----------------------------------------------------------------------
   */
  public function rules(): array
  {
    return [
      'nama_lengkap'    => ['required', 'string', 'max:255'],
      'email'           => ['required', 'email', 'unique:users_magang,email'],
      'password' => [
        'required',
        'confirmed',
        Password::min(8)
          ->mixedCase()
          ->numbers(),
      ],
      'education_level' => ['required', 'string', 'max:50'],
      'nim_nisn'        => ['nullable', 'string', 'max:50'],
      'terms'           => ['accepted'],
    ];
  }


  /**
   * -----------------------------------------------------------------------
   * messages()
   * -----------------------------------------------------------------------
   * Pesan error dalam Bahasa Indonesia agar konsisten dengan UI.
   * -----------------------------------------------------------------------
   */
  public function messages(): array
  {
    return [
      'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
      'email.required'         => 'Email wajib diisi.',
      'email.email'            => 'Format email tidak valid.',
      'email.unique'           => 'Email ini sudah terdaftar. Silakan login.',
      'password.required'      => 'Password wajib diisi.',
      'password.min'           => 'Password minimal 8 karakter.',
      'password.confirmed'     => 'Konfirmasi password tidak cocok.',
      'password.regex'         => 'Password harus mengandung minimal 1 huruf besar dan 1 angka.',
      'education_level.required' => 'Jenjang pendidikan wajib dipilih.',
      'terms.accepted'         => 'Anda harus menyetujui syarat dan ketentuan.',
    ];
  }
}
