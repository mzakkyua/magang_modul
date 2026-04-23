<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacancyMagang;

class LandingController extends Controller
{

    /**
     * ===============================================================
     * LANDING PAGE CONTROLLER
     * ===============================================================
     *
     * Controller ini bertanggung jawab untuk menampilkan
     * halaman landing page sistem magang.
     *
     * Halaman ini bersifat PUBLIC sehingga tidak membutuhkan login.
     *
     * Data yang ditampilkan:
     *
     * - Daftar lowongan magang yang masih open
     * - Daftar lowongan penelitian yang masih open
     * - Fitur pencarian lowongan
     *
     * Query difilter berdasarkan:
     *
     * status = open
     *
     * sehingga hanya lowongan aktif yang ditampilkan.
     *
     */

    public function index(Request $request)
    {

        /**
         * ===========================================================
         * SECTION 1 — SEARCH INPUT
         * ===========================================================
         *
         * Mengambil input search dari query parameter.
         *
         * Contoh URL:
         *
         * /landing?search=data
         *
         * trim() digunakan untuk menghapus whitespace
         * di awal dan akhir input.
         */

        $search = trim($request->search);



        /**
         * ===========================================================
         * SECTION 2 — BASE QUERY LOWONGAN
         * ===========================================================
         *
         * Membuat base query untuk mengambil lowongan
         * yang masih terbuka.
         *
         * Tujuan base query:
         *
         * - Menghindari duplikasi query
         * - Mempermudah maintenance
         * - Mempermudah penambahan filter di masa depan
         */

        $baseQuery = VacancyMagang::query()

            /**
             * =======================================================
             * FILTER STATUS
             * =======================================================
             *
             * Hanya menampilkan lowongan yang statusnya OPEN.
             */

            ->where('status', 'open')

            /**
             * =======================================================
             * SEARCH FILTER
             * =======================================================
             *
             * Filter search hanya dijalankan jika user
             * memasukkan keyword pencarian.
             */

            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    /**
                     * SEARCH LOGIC
                     *
                     * Kolom yang dicari:
                     *
                     * - title
                     * - division_name
                     * - description
                     */

                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('division_name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            });



        /**
         * ===========================================================
         * SECTION 3 — QUERY LOWONGAN MAGANG
         * ===========================================================
         *
         * Mengambil lowongan dengan type = magang.
         *
         * Landing page hanya menampilkan
         * maksimal 20 lowongan terbaru.
         */

        $vacanciesMagang = (clone $baseQuery)
            ->withCount([
                'applications as active_applications_count' => function ($q) {
                    $q->whereIn('status', ['pending', 'verified', 'interview', 'accepted']);
                }
            ])
            ->where('type', 'magang')
            ->latest()
            ->limit(20)
            ->get();



        /**
         * ===========================================================
         * SECTION 4 — QUERY LOWONGAN PENELITIAN
         * ===========================================================
         *
         * Mengambil lowongan penelitian.
         */

        $vacanciesPenelitian = (clone $baseQuery)
            ->withCount([
                'applications as active_applications_count' => function ($q) {
                    $q->whereIn('status', ['pending', 'verified', 'interview', 'accepted']);
                }
            ])
            ->where('type', 'penelitian')
            ->latest()
            ->get();



        /**
         * ===========================================================
         * SECTION 5 — RETURN VIEW
         * ===========================================================
         *
         * Mengirim data ke halaman landing page.
         *
         * View:
         *
         * resources/views/landing/index.blade.php
         */

        return view('landing.index', compact(
            'vacanciesMagang',
            'vacanciesPenelitian',
            'search'
        ));
    }

    /**
     * ===============================================================
     * DETAIL LOWONGAN
     * ===============================================================
     *
     * Menampilkan detail lowongan.
     * Hanya lowongan dengan status OPEN yang boleh diakses.
     */
    public function show($id)
    {
        /**
         * ===========================================================
         * AMBIL DATA LOWONGAN
         * ===========================================================
         */
        $vacancy = VacancyMagang::where('id', $id)
            ->where('status', 'open')
            ->firstOrFail();

        /**
         * ===========================================================
         * RETURN VIEW DETAIL
         * ===========================================================
         */
        return view('landing.show', compact('vacancy'));
    }
}
