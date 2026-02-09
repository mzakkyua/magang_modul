import Swal from 'sweetalert2';

// 1. CEK APAKAH SCRIPT JALAN (Lihat di Console Browser F12)
console.log("✅ Vacancy Create JS Loaded!");

document.addEventListener("DOMContentLoaded", function() {
    
    // 2. DEFINISI ELEMENT (Hati-hati Typo ID)
    const regModeSelect = document.getElementById('regMode');
    const typeSelect = document.getElementById('typeSelect');
    const minInput = document.getElementById('minInput');
    const maxInput = document.getElementById('maxInput');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const btnSave = document.getElementById('btnSave');
    const form = document.getElementById('createVacancyForm');

    // Debugging Element
    if (!btnSave) console.error("❌ Tombol Simpan (btnSave) TIDAK DITEMUKAN!");
    if (!form) console.error("❌ Form (createVacancyForm) TIDAK DITEMUKAN!");

    // --- LOGIC FUNCTIONS ---

    function toggleMemberInputs() {
        const mode = regModeSelect.value;
        const minDiv = document.getElementById('minMemberDiv');
        const maxDiv = document.getElementById('maxMemberDiv');

        if (mode === 'individu') {
            minDiv.classList.add('hidden');
            maxDiv.classList.add('hidden');
        } else {
            minDiv.classList.remove('hidden');
            maxDiv.classList.remove('hidden');

            if (mode === 'kelompok' || mode === 'hybrid') {
                minInput.setAttribute('min', 2);
                maxInput.setAttribute('min', 2);
                if(minInput.value < 2) minInput.value = 2;
                if(maxInput.value < 2) maxInput.value = 2;
            } else {
                minInput.setAttribute('min', 1);
                maxInput.setAttribute('min', 1);
            }
        }
    }

    function handleTypeChange() {
        const type = typeSelect.value;
        const hint = document.getElementById('typeHint');

        if (type === 'penelitian') {
            regModeSelect.value = 'individu';
            regModeSelect.classList.add('bg-gray-100', 'pointer-events-none'); 
            hint.classList.remove('hidden');
            toggleMemberInputs();
        } else {
            regModeSelect.classList.remove('bg-gray-100', 'pointer-events-none');
            hint.classList.add('hidden');
        }
    }

    function checkDateRange() {
        const startVal = startDateInput.value;
        const endVal = endDateInput.value;
        const warningBox = document.getElementById('dateWarning');
        const dayCountSpan = document.getElementById('dayCount');

        if (startVal && endVal) {
            const start = new Date(startVal);
            const end = new Date(endVal);
            const diffTime = end - start;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if(dayCountSpan) dayCountSpan.innerText = diffDays;

            if (diffDays >= 1 && diffDays <= 7) {
                warningBox.classList.remove('hidden');
            } else {
                warningBox.classList.add('hidden');
            }
        }
    }

    // --- EVENT LISTENERS ---

    if(regModeSelect) regModeSelect.addEventListener('change', toggleMemberInputs);
    if(typeSelect) typeSelect.addEventListener('change', handleTypeChange);
    
    if(startDateInput) startDateInput.addEventListener('change', checkDateRange);
    if(endDateInput) endDateInput.addEventListener('change', checkDateRange);

    // EVENT LISTENER TOMBOL SIMPAN (UPDATE VALIDASI)
    if(btnSave) {
        btnSave.addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah submit otomatis

            // 1. VALIDASI FRONTEND (Ngecek atribut 'required', 'min', 'type' di HTML)
            if (!form.checkValidity()) {
                // Jika ada yang kosong/salah, perintahkan browser untuk memunculkan bubble error
                // Bubble: "Please fill out this field" / "Harap isi bidang ini"
                form.reportValidity(); 
                
                // STOP DISINI! Jangan lanjut nampilin SweetAlert
                return; 
            }

            // 2. JIKA SEMUA TERISI, BARU TAMPILKAN SWEETALERT
            console.log("✅ Validasi Lolos. Menampilkan Popup...");

            Swal.fire({
                title: 'Apakah data sudah benar?',
                text: "Pastikan divisi, kuota, dan tanggal sudah sesuai.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563EB',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Cek Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log("🚀 Mengirim Data ke Server...");
                    form.submit();
                }
            });
        });
    }

    // Jalankan saat load
    if(regModeSelect) toggleMemberInputs();
    if(typeSelect) handleTypeChange();
});