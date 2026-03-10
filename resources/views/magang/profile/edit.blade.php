@extends('layouts.layoutlanding',['hideFooter'=>true])
@section('title','profile')
@section('content')
    

    <div class="max-w-6xl mx-auto py-12 px-4">
    {{-- Header Tetap --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
        <div>
            <h1 class="text-4xl font-black text-[#37517e] tracking-tight">Profil Saya</h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                Akun Peserta Magang Aktif
            </p>
        </div>
        {{-- Widget User di Kanan --}}
        <div class="flex items-center gap-3 bg-white p-3 rounded-2xl shadow-md border border-gray-100">
            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-200">
                {{ substr(auth()->user()->name ?? 'R', 0, 1) }}
            </div>
            <div class="pr-4">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Login Sebagai</p>
                <p class="text-sm font-bold text-[#37517e]">{{ auth()->user()->name ?? 'User' }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="grid lg:grid-cols-3 gap-8">
        @csrf
        @method('PUT')

{{-- Kolom Kiri: Form Utama --}}
<div class="lg:col-span-2">
    <div class="bg-white shadow-xl shadow-gray-200/50 border border-gray-100 rounded-[2.5rem] p-8 md:p-10">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i class="bi bi-person-badge-fill text-xl"></i>
            </div>
            <h2 class="text-xl font-bold text-[#37517e]">Detail Informasi</h2>
        </div>

        <div class="grid md:grid-cols-2 gap-x-6 gap-y-6">
            @php
                $fields = [
                    ['name' => 'full_name', 'label' => 'Nama Lengkap', 'val' => $profile->full_name ?? '', 'span' => 'md:col-span-2'],
                    ['name' => 'nim_nisn', 'label' => 'NIM / NISN', 'val' => $profile->nim_nisn ?? ''],
                    ['name' => 'institution_name', 'label' => 'Asal Instansi', 'val' => $profile->institution_name ?? ''],
                    // Jenjang akan diletakkan manual di bawah agar bisa menggunakan <select>
                    ['name' => 'major', 'label' => 'Jurusan', 'val' => $profile->major ?? ''],
                    ['name' => 'phone_number', 'label' => 'No. WhatsApp', 'val' => $profile->phone_number ?? ''],
                ];
            @endphp

            {{-- Input Looping --}}
            @foreach($fields as $index => $field)
                <div class="{{ $field['span'] ?? '' }} space-y-2">
                    <label class="text-[11px] font-black uppercase tracking-[0.1em] text-[#37517e] ml-1">{{ $field['label'] }}</label>
                    <input type="text" name="{{ $field['name'] }}" value="{{ old($field['name'], $field['val']) }}"
                        class="w-full bg-gray-50 border-2 border-gray-200 rounded-2xl px-5 py-4 text-sm focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none font-bold text-gray-800 placeholder:text-gray-300">
                </div>

                {{-- Sisipkan Kolom Jenjang setelah Asal Instansi (Index ke-2) --}}
                @if($index == 2)
                <div class="space-y-2">
                    <label class="text-[11px] font-black uppercase tracking-[0.1em] text-[#37517e] ml-1">Jenjang Pendidikan</label>
                    <div class="relative">
                        <select name="education_level" 
                            class="w-full bg-gray-50 border-2 border-gray-200 rounded-2xl px-5 py-4 text-sm focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none font-bold text-gray-800 appearance-none">
                            <option value="">Pilih Jenjang</option>
                            <option value="SMA" @selected(old('education_level', $profile->education_level ?? '') == 'SMA')>SMA</option>
                            <option value="SMK" @selected(old('education_level', $profile->education_level ?? '') == 'SMK')>SMK</option>
                            <option value="D3" @selected(old('education_level', $profile->education_level ?? '') == 'D3')>D3</option>
                            <option value="S1" @selected(old('education_level', $profile->education_level ?? '') == 'S1')>S1 / D4</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>
                @endif
            @endforeach

            <div class="md:col-span-2 space-y-2">
                <label class="text-[11px] font-black uppercase tracking-[0.1em] text-[#37517e] ml-1">Alamat Lengkap</label>
                <textarea name="address" rows="3" 
                    class="w-full bg-gray-50 border-2 border-gray-200 rounded-2xl px-5 py-4 text-sm focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none font-bold text-gray-800">{{ $profile->address ?? '' }}</textarea>
            </div>
        </div>
    </div>
</div>

        {{-- Kolom Rapi: Keamanan & Button --}}
        <div class="space-y-6">
            {{-- Card Keamanan --}}
            <div class="bg-[#37517e] rounded-[2.5rem] p-8 text-white shadow-2xl shadow-blue-900/30 relative overflow-hidden group">
                <div class="relative z-10 text-center">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/20">
                        <i class="bi bi-shield-lock-fill text-3xl text-blue-300"></i>
                    </div>
                    <h2 class="text-xl font-bold mb-6 tracking-tight">Update Password</h2>
                    
                    <div class="space-y-4 text-left">
                        <div class="space-y-1">
                            <input type="password" name="current_password" placeholder="Password lama"
                                class="w-full bg-white/5 border border-white/20 rounded-xl px-4 py-3.5 text-sm focus:bg-white focus:text-gray-900 transition-all outline-none font-bold placeholder:text-white/30">
                        </div>
                        <div class="space-y-1">
                            <input type="password" name="new_password" placeholder="Password baru"
                                class="w-full bg-white/5 border border-white/20 rounded-xl px-4 py-3.5 text-sm focus:bg-white focus:text-gray-900 transition-all outline-none font-bold placeholder:text-white/30">
                        </div>
                        <div class="space-y-1">
                            <input type="password" name="new_password_confirmation" placeholder="Ulangi Password Baru"
                                class="w-full bg-white/5 border border-white/20 rounded-xl px-4 py-3.5 text-sm focus:bg-white focus:text-gray-900 transition-all outline-none font-bold placeholder:text-white/30">
                        </div>
                    </div>
                    <p class="text-[10px] text-blue-200/50 mt-4 italic">*Kosongkan jika tidak ingin ganti password.</p>
                </div>
            </div>

            {{-- Button Simpan yang Lebih Tegas --}}
            <button type="submit" class="w-full bg-blue-600 text-white p-5 rounded-[2rem] font-black uppercase tracking-widest text-xs hover:bg-blue-700 hover:scale-[1.02] active:scale-95 shadow-xl shadow-blue-200 transition-all duration-300 flex items-center justify-center gap-3">
                <i class="bi bi-cloud-arrow-up-fill text-lg"></i>
                <span>Simpan Seluruh Perubahan</span>
            </button>
            
            <div class="p-6 bg-amber-50 rounded-[2rem] border border-amber-100">
                 <p class="text-[11px] font-bold text-amber-800 leading-relaxed italic text-center">
                    "Pastikan data yang Anda masukkan sudah sesuai dengan identitas resmi untuk kelancaran sertifikasi."
                 </p>
            </div>
        </div>
    </form>
</div>
@endsection