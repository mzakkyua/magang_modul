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
