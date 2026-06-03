@props(['name', 'label' => '', 'type' => 'text', 'value' => '', 'readonly' => false])

<div class="w-full relative">
    <div class="relative group">

        {{-- ICON KIRI --}}
        @isset($icon)
            <div
                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10 transition-colors duration-200 {{ $readonly ? 'text-gray-300' : 'text-gray-400 group-focus-within:text-blue-500' }}">
                {{ $icon }}
            </div>
        @endisset

        {{-- INPUT --}}
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
            value="{{ old($name, $value) }}" placeholder=" "
            @if ($readonly) readonly tabindex="-1" @endif @class([
                'peer w-full pt-5 pb-2 rounded-xl border text-sm font-medium outline-none transition-all duration-200',
                'pl-10' => isset($icon),
                'pl-3.5' => !isset($icon),
                'pr-10' => isset($append) || $type === 'password', // Otomatis padding kanan jika tipe password
                'pr-3.5' => !isset($append) && $type !== 'password',
            
                // State Normal / Writable
                'bg-white text-gray-800 border-gray-200 hover:border-blue-300 hover:bg-blue-50/20 focus:border-blue-400 focus:ring-2 focus:ring-blue-100' =>
                    !$errors->has($name) && !$readonly,
            
                // State Error
                'bg-white border-red-300 focus:ring-2 focus:ring-red-100' =>
                    $errors->has($name) && !$readonly,
            
                // State Readonly
                'bg-gray-50 border-gray-100 text-gray-400 cursor-not-allowed' => $readonly,
            ])
            {{ $attributes }}>

        {{-- FLOATING LABEL --}}
        <label for="{{ $name }}" @class([
            'absolute pointer-events-none select-none transition-all duration-200 ease-out origin-left',
            'top-2.5 text-[10.5px] font-bold',
            'peer-placeholder-shown:top-1/2 peer-placeholder-shown:-translate-y-1/2',
            'peer-placeholder-shown:text-sm peer-placeholder-shown:font-normal',
            'peer-focus:top-2.5 peer-focus:translate-y-0 peer-focus:text-[10.5px] peer-focus:font-bold',
            isset($icon) ? 'left-10' : 'left-3.5',
            $readonly
                ? 'text-gray-300'
                : ($errors->has($name)
                    ? 'text-red-400 peer-focus:text-red-500'
                    : 'text-gray-400 peer-focus:text-blue-500'),
        ])>
            {{ $label }}
        </label>

        {{-- BOTTOM BORDER SLIDE --}}
        @if (!$readonly)
            <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-xl overflow-hidden pointer-events-none">
                <div
                    class="h-full origin-left scale-x-0 group-focus-within:scale-x-100 transition-transform duration-300 ease-out {{ $errors->has($name) ? 'bg-red-500' : 'bg-blue-500' }}">
                </div>
            </div>
        @endif

        {{-- APPEND KANAN / OTOMATISASI TOMBOL MATA --}}
        @isset($append)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center z-10">
                {{ $append }}
            </div>
        @elseif($type === 'password')
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center z-10">
                <button type="button" onmousedown="event.preventDefault()"
                    onclick="let input = document.getElementById('{{ $name }}'); let icon = this.querySelector('i'); if(input.type === 'password') { input.type = 'text'; icon.classList.replace('bi-eye-slash', 'bi-eye'); } else { input.type = 'password'; icon.classList.replace('bi-eye', 'bi-eye-slash'); }"
                    class="text-gray-400 hover:text-blue-500 focus:outline-none transition-colors duration-200">
                    <i class="bi bi-eye-slash text-base"></i>
                </button>
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
