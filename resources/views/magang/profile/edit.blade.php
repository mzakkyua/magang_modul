<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profil</title>
     <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body>
    @include('layouts.layoutlanding')
   

<div class="max-w-4xl mx-auto py-10">

<h1 class="text-2xl font-bold mb-6">Profil Saya</h1>

@if(session('success'))
<div class="bg-green-100 text-green-700 p-3 rounded mb-6">
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('profile.update') }}">
@csrf
@method('PUT')

<div class="bg-white shadow rounded-xl p-6 space-y-6">

<div class="grid md:grid-cols-2 gap-4">

    <div>
        <label>Nama Lengkap</label>
        <input type="text" name="full_name"
        value="{{ old('full_name',$profile->full_name ?? '') }}"
        class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label>NIM/NISN</label>
        <input type="text" name="nim_nisn"
        value="{{ old('nim_nisn',$profile->nim_nisn ?? '') }}"
        class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label>Instansi/Sekolah</label>
        <input type="text" name="institution_name"
        value="{{ old('institution_name',$profile->institution_name ?? '') }}"
        class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label id="edu_lvl">Jenjang</label>
       <select name="education_level" id="education_level" class="border rounded px-3 py-2 w-full">
    <option value="siswa_smk"
        {{ old('education_level', $profile->education_level ?? '') == 'siswa_smk' ? 'selected' : '' }}>
        Siswa SMK
    </option>

    <option value="mahasiswa"
        {{ old('education_level', $profile->education_level ?? '') == 'mahasiswa' ? 'selected' : '' }}>
        Mahasiswa
    </option>
</select>

    </div>

    <div>
        <label>Jurusan</label>
        <input type="text" name="major"
        value="{{ old('major',$profile->major ?? '') }}"
        class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label>No HP</label>
        <input type="text" name="phone_number"
        value="{{ old('phone_number',$profile->phone_number ?? '') }}"
        class="w-full border rounded px-3 py-2">
    </div>

    <div class="md:col-span-2">
        <label>Alamat</label>
        <textarea name="address"
        class="w-full border rounded px-3 py-2">{{ $profile->address ?? '' }}</textarea>
    </div>

</div>

<hr>

<h3 class="font-semibold">Ganti Password</h3>

<div class="grid md:grid-cols-3 gap-3">
    <input type="password" name="current_password"
    placeholder="Password lama"
    class="border rounded px-3 py-2">

    <input type="password" name="new_password"
    placeholder="Password baru"
    class="border rounded px-3 py-2">

    <input type="password" name="new_password_confirmation"
    placeholder="Konfirmasi"
    class="border rounded px-3 py-2">
</div>

<button class="bg-blue-600 text-white px-6 py-2 rounded mt-4">
    Simpan
</button>

</div>
</form>
</div>


</body>
<script>
document.getElementById('education_level').addEventListener('change', function() {
    let label = document.getElementById('label_major');
    if(this.value === 'siswa_smk'){
        label.innerText = 'Jurusan SMK';
    } else {
        label.innerText = 'Program Studi';
    }
});
</script>
</html>