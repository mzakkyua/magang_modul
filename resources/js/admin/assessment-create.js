// resources/js/admin/assessment-create.js

document.addEventListener("DOMContentLoaded", function () {
    // 1. Definisikan Element
    const inputs = document.querySelectorAll(".score-input");
    const display = document.getElementById("liveFinalScore");

    // Safety Check: Jika halaman ini dibuka tapi element tidak ada, stop script.
    if (!display || inputs.length === 0) {
        return;
    }

    // 2. Fungsi Hitung Rata-rata
    function calculateAverage() {
        let total = 0;
        let count = 0;

        inputs.forEach((input) => {
            let val = parseFloat(input.value);
            if (isNaN(val)) val = 0;

            // Validasi: Cegah input > 100 atau < 0
            if (val > 100) {
                input.value = 100;
                val = 100;
            }
            if (val < 0) {
                input.value = 0;
                val = 0;
            }

            total += val;
            count++;
        });

        // Hitung Rata-rata
        const avg = count > 0 ? total / count : 0;

        // Update Teks
        display.innerText = avg.toFixed(2);

        // Update Warna Background (Visual Feedback)
        const container = display.parentElement;

        // Reset class warna lama
        container.classList.remove(
            "bg-blue-600",
            "bg-green-600",
            "bg-yellow-500",
            "bg-red-600",
        );

        // Set warna baru
        if (avg >= 85)
            container.classList.add("bg-green-600"); // A (Sangat Baik)
        else if (avg >= 70)
            container.classList.add("bg-blue-600"); // B (Baik)
        else if (avg >= 50)
            container.classList.add("bg-yellow-500"); // C (Cukup)
        else container.classList.add("bg-red-600"); // D (Kurang)
    }

    // 3. Pasang Event Listener
    inputs.forEach((input) => {
        input.addEventListener("input", calculateAverage);
        // Tambahkan event 'change' juga biar lebih responsif saat copy-paste
        input.addEventListener("change", calculateAverage);
    });

    // 4. Jalankan sekali saat load (untuk handle data edit)
    calculateAverage();
});
