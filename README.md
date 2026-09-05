# Lattice Starter Kit

A Laravel starter kit for building server-driven React applications with
[Lattice](https://latticephp.com). Pages, forms, tables, and layouts are defined in PHP and rendered
as React components over [Inertia](https://inertiajs.com) — you get a single-page-app feel without
hand-writing the client.

## Features

- **Server-driven UI** — pages, forms, tables, and layouts declared in PHP with Lattice.
- **Authentication** — login, registration, password reset, and email verification via
  [Laravel Fortify](https://laravel.com/docs/fortify), plus **two-factor authentication** and
  **passkeys**.
- **Teams** — memberships, roles, and email invitations.
- **Account settings** — profile, password, and security management.
- **Realtime notifications** — live in-app toasts powered by [Laravel Reverb](https://laravel.com/docs/reverb)
  and Echo. Invite a teammate who already has an account and they are notified instantly, wherever
  they are in the app.
- **Modern frontend** — React 19, Inertia 3, Tailwind CSS v4, and TypeScript.

## Requirements

- PHP 8.4+
- Composer
- Node.js 22+

## Getting started

Create a new project:

```bash
composer create-project lattice-php/starter-kit my-app
cd my-app
```

Then run the one-shot setup (install dependencies, create `.env`, generate the app key, migrate, and
build assets):

```bash
composer setup
```

Start the development environment — the app server, queue worker, Reverb websocket server, log
viewer, and Vite all run together:

```bash
composer dev
```

The app is now available at the URL printed by `php artisan serve`.

## Realtime

Realtime notifications run over [Laravel Reverb](https://laravel.com/docs/reverb). `composer dev`
starts the Reverb server for you; in production run it with `php artisan reverb:start`.

The broadcast connection and credentials are configured through the `REVERB_*` and `VITE_REVERB_*`
variables in `.env` (`reverb:install` generates them). The client connects in
`resources/js/app.tsx` via `configureEcho`, and pages declare their listeners in PHP — see
`app/Pages/Concerns/ListensForUserNotifications.php` for the team-invitation example.

## Project layout

There are no page components to write. A screen is a PHP class:

| Directory                  | What lives there                                                      |
| -------------------------- | --------------------------------------------------------------------- |
| `app/Pages/`               | `#[AsPage]` classes — each registers its own route                    |
| `app/Layouts/`             | `#[AsLayout]` classes — the app and auth chrome                       |
| `app/Forms/`               | `#[AsForm]` classes — fields, validation, and submit handling         |
| `app/Tables/`              | `#[AsTable]` classes — columns, sources, and row actions              |
| `app/Actions/`             | `#[AsAction]` classes — server-side effects (toast, redirect, reload) |
| `app/Components/`          | custom wire components, plus the `PageHeader` factory pages open with |
| `resources/js/components/` | the handful of custom React components, registered in `app.tsx`       |

Records reach a definition through Lattice's context registry rather than through route parameters —
the keys are registered in `AppServiceProvider::registerLatticeContext()`.

## Testing and verification

```bash
composer test          # Pint + the Unit and Feature suites, in parallel
composer test:browser  # the Pest browser suite (needs `npm run build` first)
composer ci:check      # everything CI runs: frontend checks, PHPStan, Rector, tests
```

Git hooks enforce the gate locally: `composer install` points `core.hooksPath` at `.githooks`, where
**pre-commit** formats staged files and **pre-push** runs PHPStan, the test suite, and the build,
scoped to what the push touches. Run `composer hooks:install` if the hooks are not active.

## Working with an AI agent

The kit ships agent instructions: `.ai/guidelines/` holds the project-wide guidance and `.ai/rules/`
holds path-scoped conventions (`.ai/rules/index.md` maps globs to rule files).
[Laravel Boost](https://github.com/laravel/boost) compiles them into `CLAUDE.md` / `AGENTS.md` with
`php artisan boost:update` — both are git-ignored and regenerated locally.

## License

MIT.
