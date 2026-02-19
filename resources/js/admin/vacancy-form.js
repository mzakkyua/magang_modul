console.log("vacancy.js LOADED");
console.log("checkDuration fired", startDate?.value, endDate?.value);
import Swal from "sweetalert2";

document.addEventListener("DOMContentLoaded", () => {
    // =====================================================
    // ELEMENT UTAMA
    // =====================================================
    const form = document.getElementById("vacancyForm");
    const btnSubmit = document.getElementById("btnSubmit");

    if (!form || !btnSubmit) return;

    const mode = form.dataset.mode || "create";
    const hasApplicant = form.dataset.hasApplicant === "1";

    const regMode = document.getElementById("regMode");
    const hiddenRegMode = form.querySelector('input[name="registration_mode"]');
    const typeSelect = document.getElementById("typeSelect");

    const minDiv = document.getElementById("minMemberDiv");
    const maxDiv = document.getElementById("maxMemberDiv");
    const minInput = document.getElementById("minInput");
    const maxInput = document.getElementById("maxInput");

    const startDate = document.getElementById("startDate");
    const endDate = document.getElementById("endDate");
    const dateWarning = document.getElementById("dateWarning");
    const dayCountEl = document.getElementById("dayCount");

    // =====================================================
    // MODE PENDAFTARAN (LOGIC TETAP)
    // =====================================================
    function toggleMemberInputs() {
        if (!regMode) return;

        if (regMode.value !== "individu") {
            syncMinMax();
        }

        // 🔑 SINKRONISASI KE INPUT HIDDEN
        if (hiddenRegMode) {
            hiddenRegMode.value = regMode.value;
        }

        if (regMode.value === "individu") {
            minDiv?.classList.add("hidden");
            maxDiv?.classList.add("hidden");

            if (minInput) minInput.value = 1;
            if (maxInput) maxInput.value = 1;
        } else {
            minDiv?.classList.remove("hidden");
            maxDiv?.classList.remove("hidden");

            if (minInput) {
                minInput.min = 2;
                if (minInput.value < 2) minInput.value = 2;
            }

            if (maxInput) {
                maxInput.min = 2;
                if (maxInput.value < minInput.value) {
                    maxInput.value = minInput.value;
                }
            }
        }
    }

    // =====================================================
    // TIPE PROGRAM (KHUSUS CREATE, LOGIC SAMA)
    // =====================================================
    function handleTypeChange() {
        if (!typeSelect || !regMode) return;

        const hint = document.getElementById("typeHint");

        if (typeSelect.value === "penelitian") {
            regMode.value = "individu";
            regMode.classList.add("pointer-events-none", "bg-gray-100");
            hint?.classList.remove("hidden");
            toggleMemberInputs();
        } else {
            regMode.classList.remove("pointer-events-none", "bg-gray-100");
            hint?.classList.add("hidden");
        }
    }

    // =====================================================
    // CEK DURASI MAGANG (LOGIC SAMA)
    // =====================================================
    function checkDuration() {
        if (!startDate?.value || !endDate?.value || !dateWarning) return;

        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

        if (diffDays > 0 && diffDays < 30) {
            dateWarning.classList.remove("hidden");
            if (dayCountEl) dayCountEl.innerText = diffDays;
        } else {
            dateWarning.classList.add("hidden");
        }
    }

    // =====================================================
    // SUBMIT FORM (CREATE & EDIT)
    // =====================================================
    btnSubmit.addEventListener("click", (e) => {
        e.preventDefault();

        if (!form.reportValidity()) return;

        Swal.fire({
            title: mode === "edit" ? "Update Lowongan?" : "Simpan Lowongan?",
            text:
                mode === "edit"
                    ? "Perubahan akan langsung berlaku."
                    : "Pastikan aturan pendaftaran sudah sesuai.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: mode === "edit" ? "Ya, Update" : "Ya, Simpan",
            cancelButtonText: "Cek Lagi",
            confirmButtonColor: "#2563EB",
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                btnSubmit.disabled = true;
                btnSubmit.innerText =
                    mode === "edit" ? "Mengupdate..." : "Menyimpan...";
                form.submit();
            }
        });
    });

    function syncMinMax() {
        if (!minInput || !maxInput) return;
        if (regMode.value === "individu") return;

        const min = parseInt(minInput.value || 0);
        const max = parseInt(maxInput.value || 0);

        // min minimal 2
        if (min < 2) {
            minInput.value = 2;
            return;
        }

        // ⬆️ AUTO NAIKKAN MAX JIKA DI BAWAH MIN
        if (max < min) {
            maxInput.value = min;
        }

        // batasi spinner max
        maxInput.min = min;
    }

    // =====================================================
    // EVENT LISTENER
    // =====================================================
    minInput?.addEventListener("input", syncMinMax);
    maxInput?.addEventListener("input", syncMinMax);

    if (!hasApplicant && regMode) {
        regMode.addEventListener("change", toggleMemberInputs);
    }

    typeSelect?.addEventListener("change", handleTypeChange);
    startDate?.addEventListener("change", checkDuration);
    endDate?.addEventListener("change", checkDuration);

    // =====================================================
    // INIT
    // =====================================================
    toggleMemberInputs();
    handleTypeChange();
    checkDuration();
});
