import inertia from "@inertiajs/vite";
import { lattice } from "@lattice-php/lattice/vite";
import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";
import laravel from "laravel-vite-plugin";
import path from "node:path";
import { defineConfig } from "vite";

const useLocalLattice = process.env.LATTICE_SOURCE === "1";

export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                // The rich editor and the websocket client are both large and
                // used by a minority of pages; splitting them keeps them out of
                // the entry chunk every page pays for.
                manualChunks(id) {
                    if (id.includes("prosemirror") || id.includes("@tiptap")) {
                        return "rich-editor-vendor";
                    }
                    if (id.includes("pusher-js") || id.includes("@laravel/echo")) {
                        return "echo-vendor";
                    }
                },
            },
        },
    },
    resolve: {
        alias: {
            "@": path.resolve(import.meta.dirname, "resources/js"),
        },
    },
    plugins: [
        lattice({
            source: useLocalLattice,
            icons: {
                dirs: ["resources/icons"],
            },
        }),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.tsx"],
            refresh: true,
        }),
        inertia(),
        react({
            babel: {
                plugins: ["babel-plugin-react-compiler"],
            },
        }),
        tailwindcss(),
    ],
});
