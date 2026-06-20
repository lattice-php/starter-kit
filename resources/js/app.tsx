/// <reference types="@lattice-php/lattice/svg-sprite-client" />
import { configureEcho } from "@laravel/echo-react";
import { createLatticeApp } from "@lattice-php/lattice";
import sprite from "virtual:svg-sprite";
import { registry } from "@/registry";

configureEcho({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 80),
    wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 443),
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
    enabledTransports: ["ws", "wss"],
});

const applicationName = import.meta.env.VITE_APP_NAME || "Laravel";

createLatticeApp({
    title: (title) => (title ? `${title} - ${applicationName}` : applicationName),
    registry,
    sprite,
    progress: {
        color: "#4B5563",
    },
});
