@extends('layouts.admin')

@section('title', 'Edit Lowongan')

@section('content')

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-xl font-bold text-gray-800">
                Edit Data Lowongan
            </h2>

            <span
                class="px-3 py-1 text-xs font-semibold rounded-full
            {{ $vacancy->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                STATUS: {{ strtoupper($vacancy->status) }}
            </span>
        </div>

        {{-- WARNING --}}
        @if ($hasApplicant)
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 text-sm text-yellow-800">
                ⚠️ Lowongan ini sudah memiliki pendaftar.
                Beberapa pengaturan inti dikunci.
            </div>
        @endif

        <form id="vacancyForm" data-mode="edit" data-has-applicant="{{ $hasApplicant ? '1' : '0' }}"
            action="{{ route('admin.vacancies.update', $vacancy->id) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            {{-- FORM BERSAMA --}}
            @include('admin.vacancies._form')

            {{-- ACTION --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.vacancies.index') }}" class="px-4 py-2 bg-gray-200 rounded">
                    Batal
                </a>

                <button type="button" id="btnSubmit" class="px-6 py-2 bg-blue-600 text-white rounded">
                    Update
                </button>
            </div>

        </form>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/admin/vacancy-form.js'])
@endpush
