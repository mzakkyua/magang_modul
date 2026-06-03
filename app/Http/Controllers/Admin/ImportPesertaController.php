<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserMagang;
use App\Models\ProfileMagang;
use App\Models\VacancyMagang;
use App\Models\ApplicationMagang;
use App\Models\ApplicationMemberMagang;
use App\Models\AssessmentMagang; // Ditambahkan untuk Nilai
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date; // Ditambahkan untuk baca format tanggal Excel
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportPesertaController extends Controller
{
  /**
   * =========================================================
   * FUNGSI 1: DOWNLOAD TEMPLATE EXCEL (DIPERBARUI)
   * =========================================================
   */
  public function downloadTemplate()
  {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Membuat Judul Kolom (Header) - Ditambah Posisi, Divisi, Periode & Nilai
    $headers = [
      'A1' => 'Nama Lengkap (Wajib)',
      'B1' => 'Email (Kosongkan jika tidak ada)',
      'C1' => 'NIM / NISN',
      'D1' => 'Asal Instansi / Kampus / Sekolah',
      'E1' => 'Jenjang (SMA/SMK/D3/S1/S2)',
      'F1' => 'Jurusan',
      'G1' => 'Nomor HP / WA',
      'H1' => 'Alamat Lengkap',
      'I1' => 'Posisi / Lowongan Magang',
      'J1' => 'Divisi Penempatan',
      'K1' => 'Tanggal Mulai (YYYY-MM-DD)',
      'L1' => 'Tanggal Selesai (YYYY-MM-DD)',
      'M1' => 'Nilai Akhir (0-100)'
    ];

    foreach ($headers as $cell => $text) {
      $sheet->setCellValue($cell, $text);
      $sheet->getStyle($cell)->getFont()->setBold(true);
      $sheet->getColumnDimension(substr($cell, 0, 1))->setAutoSize(true);

      // Beri warna latar belakang khusus untuk kolom penempatan (I sampai M)
      if (in_array(substr($cell, 0, 1), ['I', 'J', 'K', 'L', 'M'])) {
        $sheet->getStyle($cell)->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()->setARGB('FFF0F8FF'); // Biru muda
      }
    }

    $writer = new Xlsx($spreadsheet);

    return new StreamedResponse(function () use ($writer) {
      $writer->save('php://output');
    }, 200, [
      'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'Content-Disposition' => 'attachment; filename="Template_Import_Arsip_Peserta_V2.xlsx"',
      'Cache-Control' => 'max-age=0',
    ]);
  }

  /**
   * =========================================================
   * FUNGSI 2: PROSES DATA EXCEL YANG DIUNGGAH
   * =========================================================
   */
  public function store(Request $request)
  {
    $request->validate([
      'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
    ], [
      'file_excel.required' => 'File Excel wajib diunggah!',
      'file_excel.mimes' => 'Format file harus berupa Excel (.xlsx atau .xls)!',
    ]);

    try {
      DB::beginTransaction();

      $file = $request->file('file_excel');
      $spreadsheet = IOFactory::load($file->getPathname());
      $sheetData = $spreadsheet->getActiveSheet()->toArray();

      $importedCount = 0;

      foreach ($sheetData as $index => $row) {
        if ($index === 0) continue; // Lewati baris header

        $nama = trim($row[0] ?? '');
        if (empty($nama)) continue;

        // 1. DATA AKUN (User & Profile)
        $email = trim($row[1] ?? '');
        if (empty($email)) {
          $email = 'arsip_' . time() . '_' . rand(100, 999) . '@dummy.com';
        }

        $user = UserMagang::where('email', $email)->first();
        if (!$user) {
          $user = UserMagang::create([
            'username' => 'arsip_' . Str::random(8),
            'email' => $email,
            'email_verified_at' => now(),
            'password_hash' => Hash::make(Str::random(24)),
          ]);
        }

        ProfileMagang::updateOrCreate(
          ['user_id' => $user->id],
          [
            'full_name' => $nama,
            'nim_nisn' => trim($row[2] ?? '') ?: '-',
            'institution_name' => trim($row[3] ?? '') ?: '-',
            'education_level' => trim($row[4] ?? '') ?: '-',
            'major' => trim($row[5] ?? '') ?: '-',
            'phone_number' => trim($row[6] ?? '') ?: '-',
            'address' => trim($row[7] ?? '') ?: '-',
            'cv_file_path' => 'arsip_dummy.pdf',
          ]
        );

        // 2. DATA PENEMPATAN (Lowongan & Divisi)
        $posisi = trim($row[8] ?? '') ?: 'Arsip Magang Umum';
        $divisi = trim($row[9] ?? '') ?: 'Tanpa Divisi';

        // Helper: Baca tanggal Excel secara aman
        $tglMulai = $this->parseExcelDate($row[10] ?? null, '2000-01-01');
        $tglSelesai = $this->parseExcelDate($row[11] ?? null, '2000-12-31');
        $nilaiAkhir = floatval(trim($row[12] ?? 0));

        // Ciptakan "Ruangan/Lowongan" spesifik untuk anak ini jika belum ada
        $arsipVacancy = VacancyMagang::firstOrCreate(
          [
            'title' => $posisi . ' (Arsip)', // Ditandai arsip agar admin tahu
            'division_name' => $divisi,
            'start_date' => $tglMulai,
            'end_date' => $tglSelesai,
            'status' => VacancyMagang::STATUS_ARCHIVED // Tetap disembunyikan dari depan
          ],
          [
            'type' => VacancyMagang::TYPE_MAGANG,
            'registration_mode' => VacancyMagang::MODE_INDIVIDUAL,
            'quota_slots' => 0,
          ]
        );

        // 3. DAFTARKAN & LULUSKAN PESERTA KE RUANGAN TERSEBUT
        $application = ApplicationMagang::firstOrCreate(
          [
            'vacancy_id' => $arsipVacancy->id,
            'leader_user_id' => $user->id,
          ],
          [
            'status' => ApplicationMagang::STATUS_COMPLETED,
          ]
        );

        $member = ApplicationMemberMagang::firstOrCreate(
          [
            'application_id' => $application->id,
            'user_id' => $user->id,
          ],
          [
            'individual_status' => ApplicationMemberMagang::STATUS_FINISHED,
          ]
        );

        // 4. MASUKKAN NILAI AKHIR (Agar Tampil di Detail Peserta)
        if ($nilaiAkhir > 0) {
          AssessmentMagang::updateOrCreate(
            ['member_id' => $member->id],
            [
              'assessor_name' => 'Sistem Import Arsip',
              'score_behavior' => $nilaiAkhir,
              'score_discipline' => $nilaiAkhir,
              'score_performance' => $nilaiAkhir,
              'final_score' => $nilaiAkhir,
              'evaluation_notes' => 'Diimpor dari data arsip masa lampau.',
            ]
          );
        }

        $importedCount++;
      }

      DB::commit();

      return back()->with('success', "Berhasil mengimpor $importedCount data arsip beserta rincian lowongan dan nilainya!");
    } catch (\Exception $e) {
      DB::rollBack();
      return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
    }
  }

  /**
   * =========================================================
   * HELPER: MEMBACA FORMAT TANGGAL EXCEL YANG RUMIT
   * =========================================================
   */
  private function parseExcelDate($value, $defaultDate)
  {
    $value = trim($value);
    if (empty($value) || $value === '-') return $defaultDate;

    // Jika Excel menyimpannya sebagai "Angka Serial Excel" (misal: 44927 untuk 1 Jan 2023)
    if (is_numeric($value)) {
      try {
        return Date::excelToDateTimeObject($value)->format('Y-m-d');
      } catch (\Exception $e) {
        return $defaultDate;
      }
    }

    // Jika Admin mengetiknya sebagai Teks biasa (YYYY-MM-DD atau DD-MM-YYYY)
    if (strtotime($value) !== false) {
      return date('Y-m-d', strtotime($value));
    }

    return $defaultDate;
  }
}
