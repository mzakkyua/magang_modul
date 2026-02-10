import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", function () {
    // Cari semua form yang punya class 'form-delete'
    const deleteForms = document.querySelectorAll(".form-delete");

    deleteForms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            e.preventDefault(); // STOP! Jangan submit dulu.

            const dataName = form.getAttribute("data-name") || "Data ini";

            Swal.fire({
                title: "Yakin mau hapus?",
                text: `${dataName} akan dihapus permanen!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33", // Merah (Bahaya)
                cancelButtonColor: "#3085d6", // Biru (Batal)
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                reverseButtons: true, // Tombol batal di kiri (UX lebih aman)
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kalau user klik YA, baru kita submit form aslinya
                    form.submit();
                }
            });
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

        const nextStatusText = currentStatus === "open" ? "menutup" : "membuka";

        Swal.fire({
            title: "Konfirmasi Perubahan Status",
            text: `Anda yakin ingin ${nextStatusText} ${title}?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#2563eb", // Biru
            cancelButtonColor: "#9ca3af", // Abu
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
            confirmButtonColor: "#16a34a", // hijau
            cancelButtonColor: "#9ca3af",
            confirmButtonText: "Ya, Terima",
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
            confirmButtonColor: "#dc2626", // merah
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
                form.submit();
            }
        });
    });
});
