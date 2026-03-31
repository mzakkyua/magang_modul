import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    // ===============================
    // FUNGSI HELPER: SET LOADING BUTTON
    // ===============================
    // Fungsi ini hanya dipanggil jika user klik "Ya" di pop-up
    function setLoadingState(form) {
        const submitBtn = form.querySelector(".action-btn");
        const allActionBtns = document.querySelectorAll(".action-btn");

        allActionBtns.forEach((btn) => {
            btn.classList.add("opacity-50", "cursor-not-allowed");
            btn.style.pointerEvents = "none";
        });

        if (submitBtn) {
            submitBtn.innerHTML = "⏳ Memproses...";
        }
    }

    // ===============================
    // KONFIRMASI HAPUS (DELETE)
    // ===============================
    const deleteForms = document.querySelectorAll(".form-delete");
    deleteForms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const dataName = form.getAttribute("data-name") || "Data ini";

            Swal.fire({
                title: "Yakin mau hapus?",
                text: `${dataName} akan dihapus permanen!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // ===============================
    // KONFIRMASI TOGGLE STATUS LOWONGAN
    // ===============================
    const toggleForms = document.querySelectorAll(".form-toggle");
    toggleForms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const title = form.dataset.title || "lowongan ini";
            const currentStatus = form.dataset.status;
            const nextStatusText =
                currentStatus === "open" ? "menutup" : "membuka";

            Swal.fire({
                title: "Konfirmasi Perubahan Status",
                text: `Anda yakin ingin ${nextStatusText} ${title}?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#2563eb",
                cancelButtonColor: "#9ca3af",
                confirmButtonText: "Ya, Lanjutkan",
                cancelButtonText: "Batal",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // ===============================
    // KONFIRMASI APPROVE LAMARAN
    // ===============================
    document.querySelectorAll(".form-approve").forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const name = form.dataset.name || "pelamar ini";

            Swal.fire({
                title: "Terima Lamaran?",
                text: `Anda yakin ingin menerima ${name}?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#16a34a",
                cancelButtonColor: "#9ca3af",
                confirmButtonText: "Ya, Terima",
                cancelButtonText: "Batal",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Panggil efek loading di sini
                    setLoadingState(form);
                    form.submit();
                }
            });
        });
    });

    // ===============================
    // KONFIRMASI REJECT + ALASAN
    // ===============================
    document.querySelectorAll(".form-reject").forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const name = form.dataset.name || "pelamar ini";

            Swal.fire({
                title: "Tolak Lamaran?",
                text: `Berikan alasan penolakan untuk ${name}`,
                icon: "warning",
                input: "textarea",
                inputPlaceholder:
                    "Contoh: Berkas tidak lengkap / Tidak sesuai kriteria...",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#9ca3af",
                confirmButtonText: "Tolak",
                cancelButtonText: "Batal",
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value) {
                        return "Alasan penolakan wajib diisi";
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    form.querySelector('input[name="admin_feedback"]').value =
                        result.value;
                    // Panggil efek loading di sini
                    setLoadingState(form);
                    form.submit();
                }
            });
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const btnResign = document.getElementById("btn-trigger-resign");
    const formResign = document.getElementById("form-resign-intern-action");

    if (btnResign && formResign) {
        const applicantName = formResign.getAttribute("data-name");

        btnResign.addEventListener("click", function () {
            Swal.fire({
                // JUDUL SEKARANG SPESIFIK PENGUNDURAN DIRI
                title: "Konfirmasi Pengunduran Diri?",
                text: `Berikan alasan kenapa ${applicantName} mengundurkan diri di tengah masa magang:`,
                icon: "warning",
                input: "textarea", // Munculkan kotak input alasan
                inputPlaceholder:
                    "Tuliskan alasan spesifik (misal: sakit berat, masalah perkuliahan, dll)...",
                showCancelButton: true,
                confirmButtonColor: "#f97316", // Warna oranye orange-500
                cancelButtonColor: "#6b7280", // Warna abu-abu gray-500
                confirmButtonText: "Ya, Tandai Mundur",
                cancelButtonText: "Batal",
                inputValidator: (value) => {
                    if (!value) {
                        return "Mohon maaf, alasan WAJIB diisi agar terdokumentasi.";
                    }
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika admin mengonfirmasi dan mengisi alasan
                    // Buat input hidden baru untuk admin_feedback dan isi dengan nilainya
                    const feedbackInput = document.createElement("input");
                    feedbackInput.type = "hidden";
                    feedbackInput.name = "admin_feedback";
                    feedbackInput.value = result.value;
                    formResign.appendChild(feedbackInput);

                    // Submit form secara manual
                    formResign.submit();
                }
            });
        });
    }
});
