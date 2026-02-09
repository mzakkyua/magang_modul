import Swal from 'sweetalert2';

document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. FITUR TOGGLE PASSWORD 
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const inputElement = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (inputElement.type === 'password') {
                inputElement.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                inputElement.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    });

    // --- 2. LOGIC "DIRTY STATE" (UPDATE: Tambah Deteksi Foto) ---
    const btnSave = document.getElementById('btnSaveProfile');
    const form = document.getElementById('profileForm');
    
    const nameInput = document.querySelector('input[name="name"]');
    const emailInput = document.querySelector('input[name="email"]');
    // TAMBAHAN: Ambil input foto
    const photoInput = document.getElementById('photoInput'); 

    if (btnSave && form && nameInput && emailInput && photoInput) {
        
        const originalData = {
            name: nameInput.value,
            email: emailInput.value
        };

        function checkForChanges() {
            const currentName = nameInput.value;
            const currentEmail = emailInput.value;
            
            let passwordChanged = false;
            const currentPass = document.getElementById('current_password').value;
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('new_password_confirmation').value;

            if (currentPass.length > 0 || newPass.length > 0 || confirmPass.length > 0) {
                passwordChanged = true;
            }

            // TAMBAHAN: Cek apakah ada file yang dipilih di input foto?
            const photoChanged = photoInput.files.length > 0;

            const identityChanged = (currentName !== originalData.name) || (currentEmail !== originalData.email);

            // UPDATE KEPUTUSAN FINAL: Jika ada perubahan di salah satu (Nama/Email/Password/Foto)
            if (identityChanged || passwordChanged || photoChanged) {
                btnSave.disabled = false;
                btnSave.classList.remove('opacity-50', 'cursor-not-allowed');
                btnSave.classList.add('hover:bg-blue-700');
            } else {
                btnSave.disabled = true;
                btnSave.classList.add('opacity-50', 'cursor-not-allowed');
                btnSave.classList.remove('hover:bg-blue-700');
            }
        }

        nameInput.addEventListener('input', checkForChanges);
        emailInput.addEventListener('input', checkForChanges);
        
        // TAMBAHAN: Pasang CCTV di input foto (eventnya 'change')
        photoInput.addEventListener('change', checkForChanges);

        const passField1 = document.getElementById('current_password');
        const passField2 = document.getElementById('new_password');
        const passField3 = document.getElementById('new_password_confirmation');
        if(passField1) passField1.addEventListener('input', checkForChanges);
        if(passField2) passField2.addEventListener('input', checkForChanges);
        if(passField3) passField3.addEventListener('input', checkForChanges);
    }


    // --- 3. FITUR VALIDASI & POPUP (Sama, tapi hanya jalan kalau tombol aktif) ---
    if (btnSave && form) {
        btnSave.addEventListener('click', function(e) {
            e.preventDefault();

            // Cek apakah tombol sedang disabled (Double protection)
            if (btnSave.disabled) return;

            // ... (Logic Validasi Password yang Bapak minta sebelumnya) ...
            const currentPass = document.getElementById('current_password').value;
            const newPass = document.getElementById('new_password').value;
            const confirmPass = document.getElementById('new_password_confirmation').value;

            if (currentPass && !newPass) {
                Swal.fire({ icon: 'warning', title: 'Data Tidak Lengkap', text: 'Mohon isi Password Baru.' });
                return;
            }
            if (!currentPass && newPass) {
                Swal.fire({ icon: 'warning', title: 'Keamanan', text: 'Wajib isi Password Lama.' });
                return;
            }
            if (newPass !== confirmPass) {
                Swal.fire({ icon: 'error', title: 'Password Tidak Sama', text: 'Konfirmasi password salah.' });
                return;
            }

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Pastikan data profil Anda sudah benar.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563EB',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }
});