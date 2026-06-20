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
- **Teams** — multi-tenant teams with memberships, roles, and email invitations.
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

## Testing

```bash
composer test          # Pint + the full Pest suite (feature, unit, browser)
./vendor/bin/pest      # tests only
```

## License

MIT.
