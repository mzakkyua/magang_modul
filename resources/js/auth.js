document.addEventListener("DOMContentLoaded", function () {
    // ================= 1. PASSWORD STRENGTH CHECK =================
    // Hanya dieksekusi jika elemen strengthBar ada (di halaman Register)
    const passwordInput = document.getElementById("password");
    const strengthBar = document.getElementById("strengthBar");

    if (strengthBar && passwordInput) {
        const fill = document.getElementById("fill");
        const strengthText = document.getElementById("strength");

        passwordInput.addEventListener("input", function (e) {
            const pwd = e.target.value;

            if (pwd.length === 0) {
                strengthBar.classList.add("hidden");
                strengthText.textContent = "";
                return;
            }

            strengthBar.classList.remove("hidden");

            let percent = 0;
            let text = "Lemah";
            let bgColor = "bg-red-500";
            let textColor = "text-red-600";

            if (pwd.length >= 8) percent = 25;
            if (pwd.length >= 12) percent = 50;
            if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) percent = 60;
            if (/[0-9]/.test(pwd)) percent = 75;
            if (/[@!#$%^&*]/.test(pwd)) percent = 100;

            if (percent >= 75 && percent < 100) {
                text = "Kuat";
                bgColor = "bg-blue-500";
                textColor = "text-blue-600";
            } else if (percent === 100) {
                text = "Sangat Kuat";
                bgColor = "bg-green-500";
                textColor = "text-green-600";
            }

            fill.style.width = percent + "%";
            fill.className = `h-full transition-all duration-300 ${bgColor}`;
            strengthText.textContent = text;
            strengthText.className = `text-xs mt-1 font-semibold ${textColor}`;
        });
    }

    // ================= 2. LOADING STATE (Global untuk semua form Auth) =================
    // Cari form yang ada (Register atau Login)
    const authForm =
        document.getElementById("registerForm") ||
        document.getElementById("loginForm");

    if (authForm) {
        authForm.addEventListener("submit", function () {
            const btn = document.getElementById("submitBtn");
            if (btn) {
                btn.disabled = true;
                btn.classList.add("opacity-75", "cursor-not-allowed");

                // Gunakan teks berbeda tergantung form-nya
                const loadingText =
                    authForm.id === "registerForm"
                        ? "Mendaftarkan..."
                        : "Memproses...";

                btn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    ${loadingText}
                `;
            }
        });
    }
});

// ================= 3. PASSWORD TOGGLE (Fungsi Global) =================
// Ditaruh di luar DOMContentLoaded agar bisa dipanggil langsung dari onclick="..." di HTML
window.togglePassword = function (inputId) {
    const input = document.getElementById(inputId);
    const eyeIcon = document.getElementById("eye-" + inputId);
    const eyeSlashIcon = document.getElementById("eye-slash-" + inputId);

    if (!input) return; // Cegah error jika elemen tidak ada

    if (input.type === "password") {
        input.type = "text";
        eyeIcon.classList.remove("hidden");
        eyeIcon.classList.add("block");
        eyeSlashIcon.classList.remove("block");
        eyeSlashIcon.classList.add("hidden");
    } else {
        input.type = "password";
        eyeIcon.classList.remove("block");
        eyeIcon.classList.add("hidden");
        eyeSlashIcon.classList.remove("hidden");
        eyeSlashIcon.classList.add("block");
    }
};
