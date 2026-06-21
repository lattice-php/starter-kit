import type { UrlMethodPair } from "@inertiajs/core";
import { router } from "@inertiajs/react";
import { usePasskeyVerify } from "@laravel/passkeys/react";
import { Button } from "@lattice-php/lattice/core/components/button";
import { Spinner } from "@lattice-php/lattice/core/components/spinner";
import type { RendererComponent } from "@lattice-php/lattice/core/types";
import InputError from "@lattice-php/lattice/form/components/base/input-error";
import { IconRenderer } from "@lattice-php/lattice/icons";
import { useT } from "@lattice-php/lattice/i18n";

declare module "@lattice-php/lattice/core/types" {
    interface ComponentProps {
        "auth.passkey-verify": {
            label?: string;
            loadingLabel?: string;
            optionsUrl?: string;
            separator?: string;
            submitUrl?: string;
        };
    }
}

type PasskeyVerifyProps = {
    routes?: {
        options: UrlMethodPair;
        submit: UrlMethodPair;
    };
    label?: string;
    loadingLabel?: string;
    separator?: string;
};

function PasskeyVerify({ routes, label, loadingLabel, separator }: PasskeyVerifyProps = {}) {
    const { t } = useT("app");
    const { verify, isLoading, error, isSupported } = usePasskeyVerify({
        ...(routes && {
            routes: {
                options: routes.options.url,
                submit: routes.submit.url,
            },
        }),
        onSuccess: (response) => {
            if (response.redirect) {
                router.visit(response.redirect);
            }
        },
    });

    if (!isSupported) {
        return null;
    }

    return (
        <>
            <div className="grid gap-2">
                <Button
                    type="button"
                    variant="outline"
                    className="w-full"
                    onClick={verify}
                    disabled={isLoading}
                >
                    {isLoading ? (
                        <Spinner />
                    ) : (
                        <IconRenderer icon="key-round" className="h-4 w-4" />
                    )}
                    {isLoading
                        ? (loadingLabel ?? t("passkey.authenticating", "Authenticating..."))
                        : (label ?? t("passkey.sign-in", "Sign in with a passkey"))}
                </Button>
                {error && <InputError message={error} className="text-center" />}
            </div>

            <div className="relative my-6">
                <div className="absolute inset-0 flex items-center">
                    <div className="h-px w-full bg-lt-border" />
                </div>
                <div className="relative flex justify-center text-xs uppercase">
                    <span className="bg-lt-bg px-2 text-lt-muted-fg">
                        {separator ?? t("passkey.separator", "Or continue with email")}
                    </span>
                </div>
            </div>
        </>
    );
}

const PasskeyVerifyComponent: RendererComponent<"auth.passkey-verify"> = ({ node }) => (
    <div className="mx-auto w-full max-w-md">
        <PasskeyVerify
            label={node.props.label}
            loadingLabel={node.props.loadingLabel}
            routes={{
                options: {
                    method: "get",
                    url: node.props.optionsUrl ?? "",
                },
                submit: {
                    method: "post",
                    url: node.props.submitUrl ?? "",
                },
            }}
            separator={node.props.separator}
        />
    </div>
);

export default PasskeyVerifyComponent;
