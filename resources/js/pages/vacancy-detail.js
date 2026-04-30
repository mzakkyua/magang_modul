import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    const page = window.vacancyPage;

    if (!page) return;

    // =====================================================
    // REALTIME CHECK LOWONGAN UPDATE
    // =====================================================
    function initRealtimeCheck() {
        if (!page.snapshotUrl || !page.updatedAt) return;

        let currentUpdatedAt = page.updatedAt;
        let alreadyWarned = false;

        async function checkVacancyUpdate() {
            try {
                const response = await fetch(page.snapshotUrl, {
                    method: "GET",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                });

                if (!response.ok) return;

                const data = await response.json();

                if (
                    data.updated_at &&
                    data.updated_at !== currentUpdatedAt &&
                    !alreadyWarned
                ) {
                    alreadyWarned = true;

                    Swal.fire({
                        icon: "info",
                        title: "Lowongan Diperbarui",
                        text: "Data lowongan baru saja diubah admin. Halaman akan dimuat ulang.",
                        confirmButtonText: "Refresh Sekarang",
                        confirmButtonColor: "#2563EB",
                        allowOutsideClick: false,
                    }).then(() => {
                        window.location.reload();
                    });
                }
            } catch (error) {
                console.error("Realtime vacancy check gagal:", error);
            }
        }

        setInterval(checkVacancyUpdate, 30000);
    }

    // =====================================================
    // SWEETALERT SESSION NOTIFICATION
    // =====================================================
    function initAlerts() {
        if (page.successApply) {
            Swal.fire({
                icon: "success",
                title: "Berhasil Terdaftar!",
                text: page.successApply,
                confirmButtonColor: "#2563EB",
                confirmButtonText: "Oke",
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed && page.dashboardUrl) {
                    window.location.href = page.dashboardUrl;
                }
            });

            return;
        }

        if (page.successMessage) {
            Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: page.successMessage,
                confirmButtonColor: "#2563EB",
            });
        }

        if (page.errorMessage) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: page.errorMessage,
                confirmButtonColor: "#DC2626",
            });
        }
    }

    // =====================================================
    // ANTI DOUBLE SUBMIT
    // =====================================================
    function initSubmitProtection() {
        const form = document.getElementById("form-lamaran");
        const btnSubmit = document.getElementById("btn-submit");

        if (!form || !btnSubmit) return;

        form.addEventListener("submit", () => {
            btnSubmit.disabled = true;
            btnSubmit.classList.add("opacity-70", "cursor-not-allowed");

            btnSubmit.innerHTML = `
                <i class="bi bi-arrow-repeat animate-spin text-lg"></i>
                <span>Sedang Mengirim...</span>
            `;
        });
    }

    // =====================================================
    // CHARACTER COUNTER
    // =====================================================
    function initCharacterCounter() {
        const titleInput = document.getElementById("research_title");
        const titleCounter = document.getElementById("title_counter");

        if (titleInput && titleCounter) {
            titleInput.addEventListener("input", function () {
                titleCounter.textContent = `${this.value.length} / 255 karakter`;
            });
        }

        const abstractInput = document.getElementById("research_abstract");
        const abstractCounter = document.getElementById("abstract_counter");
        const MAX_ABSTRACT = 3000;

        if (abstractInput && abstractCounter) {
            abstractInput.addEventListener("input", function () {
                abstractCounter.textContent = `${this.value.length} / ${MAX_ABSTRACT} karakter`;
            });
        }
    }

    // =====================================================
    // TAMBAH ANGGOTA KELOMPOK
    // =====================================================
    function initAddMembers() {
        const container = document.getElementById("members-container");
        const addBtn = document.getElementById("add-member-btn");

        if (!container || !addBtn) return;

        const maxMembers = (page.maxMembers || 1) - 1;

        addBtn.addEventListener("click", () => {
            const currentInputs =
                container.querySelectorAll(".member-input").length;

            if (currentInputs >= maxMembers) {
                Swal.fire({
                    icon: "warning",
                    title: "Batas Maksimal",
                    text: `Lowongan ini maksimal ${
                        maxMembers + 1
                    } orang (termasuk Anda).`,
                    confirmButtonColor: "#2563EB",
                });
                return;
            }

            const div = document.createElement("div");
            div.className = "flex gap-2 member-input";

            div.innerHTML = `
                <input
                    type="email"
                    name="member_emails[]"
                    required
                    class="block w-full px-4 py-2.5 text-sm text-gray-900 bg-white rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                    placeholder="email.anggota@contoh.com"
                >

                <button
                    type="button"
                    class="px-3 py-2.5 bg-red-50 text-red-500 rounded-xl border border-red-100 hover:bg-red-100 transition flex-shrink-0 remove-member-btn"
                    title="Hapus anggota ini"
                >
                    <i class="bi bi-trash text-sm"></i>
                </button>
            `;

            container.appendChild(div);
        });

        container.addEventListener("click", (e) => {
            const btn = e.target.closest(".remove-member-btn");
            if (!btn) return;

            btn.closest(".member-input")?.remove();
        });
    }

    // =====================================================
    // HYBRID MODE TOGGLE
    // =====================================================
    function initHybridMode() {
        const hybridSelect = document.getElementById("hybrid-mode-select");
        const groupArea = document.getElementById("group-input-area");
        const membersContainer = document.getElementById("members-container");

        if (!hybridSelect || !groupArea || !membersContainer) return;

        function toggleGroupArea() {
            const isKelompok = hybridSelect.value === "kelompok";

            const inputs = membersContainer.querySelectorAll(
                'input[name="member_emails[]"]',
            );

            if (isKelompok) {
                groupArea.classList.remove("hidden");

                inputs.forEach((input) => {
                    input.disabled = false;
                    input.required = true;
                });
            } else {
                groupArea.classList.add("hidden");

                inputs.forEach((input) => {
                    input.disabled = true;
                    input.required = false;
                });
            }
        }

        hybridSelect.addEventListener("change", toggleGroupArea);

        toggleGroupArea();
    }

    // =====================================================
    // INIT ALL
    // =====================================================
    initRealtimeCheck();
    initAlerts();
    initSubmitProtection();
    initCharacterCounter();
    initAddMembers();
    initHybridMode();
});
