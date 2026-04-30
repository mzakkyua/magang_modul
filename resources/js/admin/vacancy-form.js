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
    // MODE PENDAFTARAN
    // =====================================================
    function toggleMemberInputs() {
        if (!regMode) return;

        // sync hidden input
        if (hiddenRegMode) {
            hiddenRegMode.value = regMode.value;
        }

        // ===============================
        // MODE INDIVIDU
        // ===============================
        if (regMode.value === "individu") {
            minDiv?.classList.add("hidden");
            maxDiv?.classList.add("hidden");

            if (minInput) {
                minInput.min = 1;
                minInput.value = 1;
                minInput.required = false;
            }

            if (maxInput) {
                maxInput.min = 1;
                maxInput.value = 1;
                maxInput.required = false;
            }

            return;
        }

        // ===============================
        // MODE KELOMPOK / HYBRID
        // ===============================
        minDiv?.classList.remove("hidden");
        maxDiv?.classList.remove("hidden");

        if (minInput) {
            minInput.min = 2;
            minInput.required = true;

            if (parseInt(minInput.value || 0) < 2) {
                minInput.value = 2;
            }
        }

        if (maxInput) {
            maxInput.min = 2;
            maxInput.required = true;

            if (parseInt(maxInput.value || 0) < parseInt(minInput.value || 2)) {
                maxInput.value = minInput.value;
            }
        }

        syncMinMax();
    }

    // =====================================================
    // TIPE PROGRAM
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
    // DURASI MAGANG
    // =====================================================
    function checkDuration() {
        if (!startDate?.value || !endDate?.value || !dateWarning) return;

        const start = new Date(startDate.value);
        const end = new Date(endDate.value);

        const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

        // ===============================
        // END DATE < START DATE
        // ===============================
        if (diffDays < 0) {
            dateWarning.classList.remove("hidden");
            dateWarning.classList.remove(
                "bg-amber-50",
                "border-amber-200",
                "text-amber-700",
            );
            dateWarning.classList.add(
                "bg-red-50",
                "border-red-200",
                "text-red-700",
            );

            dateWarning.innerHTML = `
            <i class="bi bi-x-circle-fill text-red-500 shrink-0 mt-0.5"></i>
            <p>Tanggal selesai tidak boleh sebelum tanggal mulai.</p>
        `;
            return;
        }

        // ===============================
        // SAME DAY
        // ===============================
        if (diffDays === 0) {
            dateWarning.classList.remove("hidden");
            dateWarning.classList.remove(
                "bg-amber-50",
                "border-amber-200",
                "text-amber-700",
            );
            dateWarning.classList.add(
                "bg-red-50",
                "border-red-200",
                "text-red-700",
            );

            dateWarning.innerHTML = `
            <i class="bi bi-x-circle-fill text-red-500 shrink-0 mt-0.5"></i>
            <p>Tanggal mulai dan selesai tidak boleh sama.</p>
        `;
            return;
        }

        // ===============================
        // SHORT DURATION
        // ===============================
        if (diffDays < 30) {
            dateWarning.classList.remove("hidden");
            dateWarning.classList.remove(
                "bg-red-50",
                "border-red-200",
                "text-red-700",
            );
            dateWarning.classList.add(
                "bg-amber-50",
                "border-amber-200",
                "text-amber-700",
            );

            dateWarning.innerHTML = `
            <i class="bi bi-exclamation-triangle-fill text-amber-500 shrink-0 mt-0.5"></i>
            <p>
                Perhatian: Durasi magang sangat singkat
                (<strong>${diffDays}</strong> hari).
                Pastikan tanggal sudah benar.
            </p>
        `;
            return;
        }

        // ===============================
        // NORMAL
        // ===============================
        dateWarning.classList.add("hidden");
    }

    // =====================================================
    // SUBMIT FORM
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

    // =====================================================
    // SYNC MIN MAX
    // =====================================================
    function syncMinMax() {
        if (!minInput || !maxInput || !regMode) return;
        if (regMode.value === "individu") return;

        const min = parseInt(minInput.value || 0);
        const max = parseInt(maxInput.value || 0);

        if (min < 2) {
            minInput.value = 2;
            return;
        }

        if (max < min) {
            maxInput.value = min;
        }

        maxInput.min = min;
    }

    // =====================================================
    // EVENT
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
