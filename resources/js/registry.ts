import {
    createPlugin,
    extendRegistry,
    lazyComponent,
    registry as packageRegistry,
} from "@lattice-php/lattice";

export const registry = extendRegistry(
    packageRegistry,
    createPlugin({
        components: {
            "auth.passkey-verify": lazyComponent(() => import("@/components/passkey-verify")),
            "settings.passkey-registration": lazyComponent(
                () => import("@/components/passkey-registration"),
            ),
        },
        name: "app",
    }),
);
