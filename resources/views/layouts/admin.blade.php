<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token">
    <title>Admin - @yield('title', 'Dashboard')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin/delete-confirm.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body class="bg-slate-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        @include('partials.sidebar')

        {{-- CONTENT --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- HEADER --}}
            <header class="h-16 flex items-center justify-between px-6 bg-white border-b border-slate-200">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        @yield('title')
                    </h2>
                    <p class="text-xs text-slate-500">
                        {{ Auth::user()->name }} • {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </header>

            {{-- MAIN --}}
            <main class="flex-1 overflow-y-auto bg-slate-100 p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg">
                        <strong>Berhasil!</strong> {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg">
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                @endif

                @yield('content')

            </main>
        </div>
    </div>
    @stack('styles')
    @stack('scripts')
</body>

</html>
