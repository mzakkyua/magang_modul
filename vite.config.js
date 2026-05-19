import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/admin/vacancy-form.js",
                "resources/js/admin/delete-confirm.js",
                "resources/js/admin/assessment-create.js",
                "resources/js/admin/profile.js",
                "resources/js/auth.js",
                "resources/js/pages/vacancy-detail.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
