/// <reference types="@lattice-php/lattice/svg-sprite-client" />
/// <reference types="@lattice-php/lattice/vite-client" />
import { createInertiaApp } from "@inertiajs/react";
import { configureEcho } from "@laravel/echo-react";
import {
    createLayoutResolver,
    createPageResolver,
    initializeTheme,
    Provider,
    withVisitHeaders,
} from "@lattice-php/lattice";
import { configureI18nFromPageProps, LocaleReload } from "@lattice-php/lattice/i18n";
import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
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

void createInertiaApp({
    title: (title) => (title ? `${title} - ${applicationName}` : applicationName),
    resolve: createPageResolver({}),
    layout: createLayoutResolver(),
    progress: {
        color: "#4B5563",
    },
    defaults: {
        visitOptions: withVisitHeaders,
    },
    setup({ el, App, props }) {
        if (!el) {
            return;
        }

        const root = createRoot(el);
        const render = () =>
            root.render(
                <StrictMode>
                    <Provider registry={registry} sprite={sprite}>
                        <App {...props} />
                        <LocaleReload />
                    </Provider>
                </StrictMode>,
            );

        void configureI18nFromPageProps(props.initialPage.props, {
            namespaces: ["lattice", "app"],
        }).then(render, render);
    },
});

initializeTheme();
