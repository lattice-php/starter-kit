import { usePasskeyRegister } from "@laravel/passkeys/react";
import type { RendererComponent } from "@lattice-php/lattice";
import { Button, Input, InputError, Label } from "@lattice-php/lattice/ui";
import { useT } from "@lattice-php/lattice/i18n";
import { useState } from "react";

declare module "@lattice-php/lattice" {
    interface ComponentProps {
        "settings.passkey-registration": Record<string, never>;
    }
}

function suggestedPasskeyName(connector: string): string {
    const ua = navigator.userAgent;

    const browser = ["Chrome", "Firefox", "Safari", "Edge", "Opera"].find((candidate) =>
        new RegExp(candidate).test(ua),
    );

    const os = ["iPhone", "iPad", "Android", "Mac", "Windows"].find((candidate) =>
        new RegExp(candidate).test(ua),
    );

    return [browser, os].filter(Boolean).join(` ${connector} `) || "";
}

const PasskeyRegistration: RendererComponent<"settings.passkey-registration"> = () => {
    const { t } = useT("app");
    const [name, setName] = useState(() => suggestedPasskeyName(t("passkey.on", "on")));
    const [showForm, setShowForm] = useState(false);
    const { register, isLoading, error, isSupported } = usePasskeyRegister({
        onSuccess: () => {
            setName("");
            setShowForm(false);
            window.dispatchEvent(
                new CustomEvent("lattice:reload-component", {
                    detail: {
                        component: "settings.passkeys",
                    },
                }),
            );
        },
    });

    async function handleSubmit(event: React.FormEvent): Promise<void> {
        event.preventDefault();

        if (name.trim()) {
            await register(name);
        }
    }

    if (!isSupported) {
        return (
            <div className="text-sm text-lt-muted-fg">
                {t("passkey.not-supported", "Passkeys are not supported in this browser.")}
            </div>
        );
    }

    if (!showForm) {
        return (
            <Button emphasis="outline" onClick={() => setShowForm(true)}>
                {t("passkey.add", "Add passkey")}
            </Button>
        );
    }

    return (
        <form
            onSubmit={handleSubmit}
            className="space-y-4 rounded-lt border border-lt-border bg-lt-muted/50 p-4"
        >
            <div className="grid gap-2">
                <Label htmlFor="passkey-name">{t("passkey.name-label", "Passkey name")}</Label>
                <Input
                    id="passkey-name"
                    type="text"
                    value={name}
                    onChange={(event) => setName(event.target.value)}
                    placeholder={t("passkey.name-placeholder", "e.g., MacBook Pro, iPhone")}
                    className="mt-1 block w-full"
                    autoFocus
                />
                <p className="text-xs text-lt-muted-fg">
                    {t("passkey.name-help", "A name helps you identify this passkey later.")}
                </p>
            </div>

            {error && <InputError message={error} />}

            <div className="flex gap-2">
                <Button type="submit" disabled={isLoading || !name.trim()}>
                    {isLoading
                        ? t("passkey.registering", "Registering...")
                        : t("passkey.register", "Register passkey")}
                </Button>
                <Button
                    type="button"
                    emphasis="ghost"
                    onClick={() => {
                        setShowForm(false);
                        setName("");
                    }}
                >
                    {t("passkey.cancel", "Cancel")}
                </Button>
            </div>
        </form>
    );
};

export default PasskeyRegistration;
