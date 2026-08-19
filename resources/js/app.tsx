/// <reference types="@lattice-php/lattice/svg-sprite-client" />
/// <reference types="@lattice-php/lattice/vite-client" />
import { configureEcho } from "@laravel/echo-react";
import { createLatticeApp, lazyComponent, type Plugin } from "@lattice-php/lattice";
import plugins from "virtual:lattice/plugins";
import sprite from "virtual:svg-sprite";

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

void createLatticeApp({
    title: (title) => (title ? `${title} - ${applicationName}` : applicationName),
    progress: {
        color: "#4B5563",
    },
    sprite,
    plugins: [
        {
            name: "app",
            components: {
                "auth.passkey-verify": lazyComponent(() => import("@/components/passkey-verify")),
                "settings.passkey-registration": lazyComponent(
                    () => import("@/components/passkey-registration"),
                ),
            },
        } satisfies Plugin,
        ...plugins,
    ],
    i18n: {
        namespaces: ["lattice", "app"],
    },
});
