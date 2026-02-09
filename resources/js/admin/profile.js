import Swal from 'sweetalert2';

document.addEventListener("DOMContentLoaded", function() {
    
    // --- DEFINISI ELEMENT (Global di dalam scope ini) ---
    const btnSave = document.getElementById('btnSaveProfile');
    const form = document.getElementById('profileForm');
    
    const nameInput = document.querySelector('input[name="name"]');
    const emailInput = document.querySelector('input[name="email"]');
    const photoInput = document.getElementById('photoInput'); 
    
    // Element fitur hapus foto
    const btnDeletePhoto = document.getElementById('btnDeletePhoto');
    const deletePhotoInput = document.getElementById('deletePhotoInput');
    const photoPreview = document.getElementById('photoPreview');
    const initialPlaceholder = document.getElementById('initialPlaceholder');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    // Element Password
    const currentPassInput = document.getElementById('current_password');
    const newPassInput = document.getElementById('new_password');
    const confirmPassInput = document.getElementById('new_password_confirmation');

    // 1. SIMPAN DATA AWAL (Untuk pembanding)
    let originalData = {};
    if (nameInput && emailInput) {
        originalData = {
            name: nameInput.value,
            email: emailInput.value
        };
    }

    // 2. FUNGSI UTAMA: CEK PERUBAHAN (DIRTY STATE)
    function checkForChanges() {
        if (!btnSave || !nameInput) return;

        // A. Cek Identitas
        const currentName = nameInput.value;
        const currentEmail = emailInput.value;
        const identityChanged = (currentName !== originalData.name) || (currentEmail !== originalData.email);

        // B. Cek Password (Apakah ada isinya?)
        let passwordChanged = false;
        if (currentPassInput.value.length > 0 || newPassInput.value.length > 0 || confirmPassInput.value.length > 0) {
            passwordChanged = true;
        }

        // C. Cek Foto Upload (Apakah user pilih file?)
        const photoUploaded = photoInput && photoInput.files.length > 0;

        // D. Cek Hapus Foto (Apakah input hidden bernilai '1'?)
        // INI YANG KEMARIN KURANG JALAN
        const photoDeleted = deletePhotoInput && deletePhotoInput.value === '1';

        // KEPUTUSAN FINAL: Aktif jika ada salah satu perubahan
        if (identityChanged || passwordChanged || photoUploaded || photoDeleted) {
            btnSave.disabled = false;
            btnSave.classList.remove('opacity-50', 'cursor-not-allowed');
            btnSave.classList.add('hover:bg-blue-700');
        } else {
            btnSave.disabled = true;
            btnSave.classList.add('opacity-50', 'cursor-not-allowed');
            btnSave.classList.remove('hover:bg-blue-700');
        }
    }

    // 3. PASANG EVENT LISTENER (CCTV)
    if (nameInput) nameInput.addEventListener('input', checkForChanges);
    if (emailInput) emailInput.addEventListener('input', checkForChanges);
    
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            // Kalau user upload file, reset status hapus
            if (this.files.length > 0 && deletePhotoInput) {
                deletePhotoInput.value = '0'; 
            }
            checkForChanges();
        });
    }

    if (currentPassInput) currentPassInput.addEventListener('input', checkForChanges);
    if (newPassInput) newPassInput.addEventListener('input', checkForChanges);
    if (confirmPassInput) confirmPassInput.addEventListener('input', checkForChanges);


    // 4. LOGIC TOMBOL HAPUS FOTO
    if (btnDeletePhoto) {
        btnDeletePhoto.addEventListener('click', function() {
            // UI Updates
            if(photoPreview) photoPreview.classList.add('hidden');
            if(initialPlaceholder) initialPlaceholder.classList.remove('hidden');
            this.classList.add('hidden'); // Sembunyikan tombol sampah

            // Form Updates
            if(photoInput) photoInput.value = ''; // Reset input file
            if(fileNameDisplay) fileNameDisplay.innerText = 'Foto akan dihapus';
            
            // SET FLAG HAPUS & CEK PERUBAHAN
            if(deletePhotoInput) {
                deletePhotoInput.value = '1'; // Tandai dihapus
                checkForChanges(); // <--- PANGGIL FUNGSI INI AGAR TOMBOL NYALA
            }
        });
    }

    // 5. FITUR TOGGLE PASSWORD
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

    // 6. SUBMIT FORM & VALIDASI
    if (btnSave && form) {
        btnSave.addEventListener('click', function(e) {
            e.preventDefault();
            if (btnSave.disabled) return;

            // Validasi Password Logic
            const cPass = currentPassInput.value;
            const nPass = newPassInput.value;
            const coPass = confirmPassInput.value;

            if (cPass && !nPass) {
                Swal.fire({ icon: 'warning', title: 'Data Tidak Lengkap', text: 'Mohon isi Password Baru.' });
                return;
            }
            if (!cPass && nPass) {
                Swal.fire({ icon: 'warning', title: 'Keamanan', text: 'Wajib isi Password Lama.' });
                return;
            }
            if (nPass !== coPass) {
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
                    // Loading State
                    const btnText = document.getElementById('btnText');
                    const btnLoading = document.getElementById('btnLoading');
                    if(btnText && btnLoading) {

                        // Sembunyikan Teks Normal
                        btnText.classList.add('hidden');
                        
                        // Munculkan Loading
                        btnLoading.classList.remove('hidden');
                        btnLoading.classList.add('flex');
                    }
                    btnSave.disabled = true;

                    // Submit Form
                    form.submit();
                }
            });
        });
    }
});