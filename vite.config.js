import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/js/home.js",

                // Academics
                "resources/js/academics/major_subjects/index.js",
                // Major
                "resources/js/academics/majors/index.js",

                // Studennt
                "resources/js/academics/students/index.js",
                "resources/js/academics/students/input.js",

                // Subject
                "resources/js/academics/subjects/index.js",

                // Evaluations
                "resources/js/evaluations/grades/index.js",
                "resources/js/evaluations/grades/input.js",
                "resources/js/evaluations/grades/show.js",

                "resources/js/evaluations/recommendations/index.js",
                "resources/js/evaluations/recommendations/create.js",
                "resources/js/evaluations/recommendations/show.js",

                // Settings
                "resources/js/settings/roles/index.js",
                "resources/js/settings/roles/input.js",
                "resources/js/settings/users/index.js",

                // Utils
                "resources/js/utils/tooltip.js",
            ],
            refresh: true,
        }),
    ],
});
