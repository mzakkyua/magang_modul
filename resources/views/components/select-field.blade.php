@props(['name', 'label' => ''])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-gray-700 text-sm font-bold mb-2">
        {{ $label }}
    </label>

    <div class="relative group">

        {{-- 1. Bagian ICON SLOT (Sama persis dengan input-field) --}}
        @isset($icon)
            <div
                class="absolute inset-y-0 left-0  flex items-center pointer-events-none
                    text-gray-400 transition-all duration-200
                    group-hover:text-blue-500
                    group-focus-within:text-blue-500">
                {{ $icon }}
            </div>
        @endisset

        {{-- 2. Tag Select dengan Padding yang Dinamis --}}
        <select name="{{ $name }}" id="{{ $name }}" @class([
            'peer w-full py-2 pr-3 border border-gray-300 rounded-lg',
            'transition-all duration-200 group-hover:border-blue-400',
            'focus:border-blue-500 focus:ring-2 focus:ring-blue-200',
            'focus:outline-none hover:bg-blue-50 focus:shadow-sm',
        
            // Kondisi Padding (Menghilangkan bentrok di VS Code)
            'pl-10' => isset($icon),
            'pl-3.5' => !isset($icon),
        
            // Kondisi Error
            'border-red-500' => $errors->has($name),
        ]) required>

            <option value="" class="text-gray-400">-- Pilih {{ $label }} --</option>

            {{ $slot }}

        </select>

        {{-- (Opsional) Ikon panah bawaan browser select kadang jelek. 
            Kita bisa menimpanya dengan ikon chevron kustom di sini jika mau. 
            Untuk sekarang kita biarkan default dulu. --}}
    </div>

    @error($name)
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>
