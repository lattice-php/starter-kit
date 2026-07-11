import {
    createPlugin,
    extendRegistry,
    lazyComponent,
    registry as packageRegistry,
} from "@lattice-php/lattice";
import plugins from "virtual:lattice/plugins";

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
    ...plugins,
);
