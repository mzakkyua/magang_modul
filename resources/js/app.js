import "./bootstrap";
import Swal from "sweetalert2";
window.Swal = Swal;

document.addEventListener("DOMContentLoaded", function () {
    const tabs = ["semua", "magang", "penelitian"];
    const buttons = document.querySelectorAll(".tab-btn");

    tabs.forEach((t) => {
        if (t !== "semua") {
            const tabElement = document.getElementById("tab-" + t);
            if (tabElement) tabElement.classList.add("hidden");
        }
    });

    buttons.forEach((button) => {
        button.addEventListener("click", function () {
            const tab = this.dataset.tab;

            // reset semua tombol
            buttons.forEach((btn) => {
                btn.classList.remove(
                    "border-b-2",
                    "border-blue-600",
                    "font-semibold",
                );
                btn.classList.add("text-gray-500");
            });

            // aktifkan tombol yang diklik
            this.classList.add(
                "border-b-2",
                "border-blue-600",
                "font-semibold",
            );
            this.classList.remove("text-gray-500");

            // sembunyikan semua tab
            tabs.forEach((t) => {
                const tabElement = document.getElementById("tab-" + t);
                if (tabElement) {
                    tabElement.classList.add("hidden");
                }
            });

            // tampilkan tab aktif
            const activeTab = document.getElementById("tab-" + tab);
            if (activeTab) {
                activeTab.classList.remove("hidden");
            }
        });
    });
});

window.openModal = function (title, category, image, description) {
    document.getElementById("modalTitle").innerText = title;
    document.getElementById("modalCategory").innerText = category;
    document.getElementById("modalImage").src = image;
    document.getElementById("modalDescription").innerText = description;

    const modal = document.getElementById("programModal");

    modal.classList.remove("hidden");
    modal.classList.add("flex");
};

window.closeModal = function () {
    const modal = document.getElementById("programModal");

    modal.classList.add("hidden");
    modal.classList.remove("flex");
};
