@props(['name', 'label' => '', 'type' => 'text', 'placeholder' => null])

{{--
    PERUBAHAN DARI VERSI SEBELUMNYA:
    1. Floating label  — label bergerak naik saat input diisi/fokus
    2. Bottom border slide — garis biru animasi dari kiri ke kanan saat fokus
    3. Focus glow ring — halo biru halus saat fokus
    4. Polished style — border, radius, warna lebih refined
    
    CATATAN TEKNIS:
    - placeholder=" " (spasi) dipakai agar :placeholder-shown CSS trick bekerja
    - Prop $placeholder yang lama tidak dirender sebagai atribut HTML
      karena floating label sudah menggantikan fungsi placeholder secara visual
    - Label harus di-render SETELAH <input> agar CSS sibling selector (~) bekerja
    - Semua slot ($icon, $append) dan $attributes tetap berfungsi normal
--}}

<div class="mb-5">
    <div class="relative group">

        {{-- ICON KIRI (Slot icon) --}}
        @isset($icon)
            <div
                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10
                        text-gray-400 transition-colors duration-200
                        group-focus-within:text-blue-500">
                {{ $icon }}
            </div>
        @endisset

        {{-- INPUT --}}
        {{--
            PENTING: placeholder=" " (satu spasi) adalah syarat floating label CSS-only.
            Saat input kosong → :placeholder-shown = true → label di tengah.
            Saat input terisi → :placeholder-shown = false → label naik (kelas base berlaku).
        --}}
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name) }}"
            placeholder=" " @class([
                'peer w-full pt-5 pb-2 rounded-xl border bg-white',
                'text-sm font-medium text-gray-800',
                'transition-all duration-200 outline-none',
            
                // Padding kiri: sesuaikan dengan ada/tidaknya icon
                'pl-10' => isset($icon),
                'pl-3.5' => !isset($icon),
            
                // Padding kanan: sesuaikan dengan ada/tidaknya append
                'pr-10' => isset($append),
                'pr-3.5' => !isset($append),
            
                // State normal
                'border-gray-200 hover:border-blue-300 hover:bg-blue-50/20
                             focus:border-blue-400 focus:bg-white
                             focus:ring-2 focus:ring-blue-100 focus:ring-offset-0' => !$errors->has(
                    $name),
            
                // State error
                'border-red-300 bg-red-50/30
                             focus:border-red-400 focus:ring-2 focus:ring-red-100 focus:ring-offset-0' => $errors->has(
                    $name),
            ]) {{ $attributes }}>

        {{-- FLOATING LABEL --}}
        {{--
            Base class = posisi floated (label sudah naik ke atas).
            peer-placeholder-shown:* = override saat input kosong (label turun ke tengah).
            peer-focus:* = override saat fokus (label naik + biru), menang atas placeholder-shown.
        --}}
        <label for="{{ $name }}"
            class="absolute pointer-events-none select-none
                   transition-all duration-200 ease-out origin-left

                   top-2.5 text-[10.5px] font-bold

                   peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2
                   peer-placeholder-shown:text-sm peer-placeholder-shown:font-normal

                   peer-focus:top-2.5 peer-focus:translate-y-0
                   peer-focus:text-[10.5px] peer-focus:font-bold

                   {{ isset($icon) ? 'left-10' : 'left-3.5' }}

                   {{ $errors->has($name)
                       ? 'text-red-400 peer-placeholder-shown:text-red-400 peer-focus:text-red-500'
                       : 'text-gray-400 peer-placeholder-shown:text-gray-400 peer-focus:text-blue-500' }}">
            {{ $label }}
        </label>

        {{-- BOTTOM BORDER SLIDE --}}
        {{--
            Div ini menempel di bawah input.
            scaleX(0) → scaleX(1) saat group-focus-within (parent .group mendapat fokus).
            origin-left: animasi mulai dari kiri ke kanan.
        --}}
        <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-xl overflow-hidden pointer-events-none">
            <div
                class="h-full origin-left scale-x-0 group-focus-within:scale-x-100
                        transition-transform duration-300 ease-out
                        {{ $errors->has($name) ? 'bg-red-500' : 'bg-blue-500' }}">
            </div>
        </div>

        {{-- ELEMEN KANAN (Slot append) untuk tombol Show/Hide password --}}
        @isset($append)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center z-10">
                {{ $append }}
            </div>
        @endisset

    </div>

    {{-- ERROR MESSAGE --}}
    @error($name)
        <p class="flex items-center gap-1.5 text-red-500 text-xs font-medium mt-1.5">
            <i class="bi bi-exclamation-circle-fill text-[11px]"></i>
            {{ $message }}
        </p>
    @enderror
</div>
