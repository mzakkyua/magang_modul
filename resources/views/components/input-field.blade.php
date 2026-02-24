@props(['name', 'label' => '', 'type' => 'text', 'placeholder' => null])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-gray-700 text-sm font-bold mb-2">
        {{ $label }}
    </label>

    <div class="relative group">

        {{-- ICON KIRI (Slot icon) --}}
        @isset($icon)
            <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none
                        text-gray-400 transition-all duration-200
                        group-hover:text-blue-500 group-focus-within:text-blue-500">
                {{ $icon }}
            </div>
        @endisset

        {{-- TAG INPUT DENGAN PADDING DINAMIS --}}
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name) }}"
            @class([
                'peer w-full py-2 border border-gray-300 rounded-lg',
                'transition-all duration-200 group-hover:border-blue-400',
                'focus:border-blue-500 focus:ring-2 focus:ring-blue-200',
                'focus:outline-none hover:bg-blue-50 focus:shadow-sm',
            
                // Atur padding kiri jika ada icon
                'pl-10' => isset($icon),
                'pl-3' => !isset($icon),
            
                // Atur padding kanan jika ada elemen tambahan (append)
                'pr-10' => isset($append),
                'pr-3' => !isset($append),
            
                // Atur border merah jika ada error
                'border-red-500' => $errors->has($name),
            ]) placeholder="{{ $placeholder ?? 'Masukkan ' . strtolower($label) }}"
            {{ $attributes }}> {{-- Memungkinkan kita menambah atribut seperti minlength --}}

        {{-- ELEMEN KANAN (Slot append) untuk tombol Show/Hide --}}
        @isset($append)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                {{ $append }}
            </div>
        @endisset

    </div>

    @error($name)
        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>
