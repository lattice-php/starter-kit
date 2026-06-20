import { usePasskeyRegister } from "@laravel/passkeys/react";
import { Button } from "@lattice-php/lattice/core/components/button";
import type { RendererComponent } from "@lattice-php/lattice/core/types";
import { Input } from "@lattice-php/lattice/form/components/base/input";
import InputError from "@lattice-php/lattice/form/components/base/input-error";
import { Label } from "@lattice-php/lattice/form/components/base/label";
import { useState } from "react";

declare module "@lattice-php/lattice/core/types" {
    interface ComponentProps {
        "settings.passkey-registration": Record<string, never>;
    }
}

function suggestedPasskeyName(): string {
    const ua = navigator.userAgent;

    const browser = ["Chrome", "Firefox", "Safari", "Edge", "Opera"].find((candidate) =>
        new RegExp(candidate).test(ua),
    );

    const os = ["iPhone", "iPad", "Android", "Mac", "Windows"].find((candidate) =>
        new RegExp(candidate).test(ua),
    );

    return [browser, os].filter(Boolean).join(" on ") || "";
}

const PasskeyRegistration: RendererComponent<"settings.passkey-registration"> = () => {
    const [name, setName] = useState(suggestedPasskeyName);
    const [showForm, setShowForm] = useState(false);
    const { register, isLoading, error, isSupported } = usePasskeyRegister({
        onSuccess: () => {
            setName("");
            setShowForm(false);
            window.dispatchEvent(
                new CustomEvent("lattice:reload-component", {
                    detail: {
                        component: "settings.passkeys",
                        type: "reloadComponent",
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
                Passkeys are not supported in this browser.
            </div>
        );
    }

    if (!showForm) {
        return (
            <Button variant="outline" onClick={() => setShowForm(true)}>
                Add passkey
            </Button>
        );
    }

    return (
        <form
            onSubmit={handleSubmit}
            className="space-y-4 rounded-lt border border-lt-border bg-lt-muted/50 p-4"
        >
            <div className="grid gap-2">
                <Label htmlFor="passkey-name">Passkey name</Label>
                <Input
                    id="passkey-name"
                    type="text"
                    value={name}
                    onChange={(event) => setName(event.target.value)}
                    placeholder="e.g., MacBook Pro, iPhone"
                    className="mt-1 block w-full"
                    autoFocus
                />
                <p className="text-xs text-lt-muted-fg">
                    A name helps you identify this passkey later.
                </p>
            </div>

            {error && <InputError message={error} />}

            <div className="flex gap-2">
                <Button type="submit" disabled={isLoading || !name.trim()}>
                    {isLoading ? "Registering..." : "Register passkey"}
                </Button>
                <Button
                    type="button"
                    variant="ghost"
                    onClick={() => {
                        setShowForm(false);
                        setName("");
                    }}
                >
                    Cancel
                </Button>
            </div>
        </form>
    );
};

export default PasskeyRegistration;
