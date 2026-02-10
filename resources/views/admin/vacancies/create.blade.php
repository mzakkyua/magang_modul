@extends('layouts.admin')

@section('title', 'Buat Lowongan Baru')

@section('content')

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">

        <form id="vacancyForm" data-mode="create" action="{{ route('admin.vacancies.store') }}" method="POST" novalidate>
            @csrf

            {{-- PANGGIL FORM BERSAMA --}}
            @include('admin.vacancies._form')

            {{-- ACTION --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.vacancies.index') }}" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">
                    Batal
                </a>

                <button type="button" id="btnSubmit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan Lowongan
                </button>
            </div>

        </form>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/vacancy-form.js'])
@endpush
