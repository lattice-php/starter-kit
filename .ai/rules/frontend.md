---
paths:
    - "resources/js/**"
    - "resources/css/**"
    - "vite.config.ts"
---

# Frontend

## There are no page components

`resources/js/app.tsx` boots `createLatticeApp` and registers the handful of custom React components by string key.
A new screen is a PHP page class, not a `.tsx` file. Write React only for behavior the wire format cannot express —
the passkey components are the example: they talk to `@laravel/passkeys` in the browser.

## Import from the package that owns the component

Form controls (`Input`, `Label`, `InputError`, …) live in `@lattice-php/form`; layout and display components live in
`@lattice-php/ui`; the app runtime and `RendererComponent` come from `@lattice-php/lattice`. Both packages are direct
dependencies — do not reach through the umbrella for something it does not re-export.

## Wire types are generated

`resources/js/lattice/generated.d.ts` declares the props of every `#[AsComponent]` class. Do not hand-write a
`declare module "@lattice-php/core"` block for a component that has a PHP counterpart; run
`php artisan lattice:typescript` instead.

## Tailwind has to be pointed at the Lattice packages

`@lattice-php/ui/css` sources its own `dist`, and the Vite plugin sources every discovered _component_ package — but
neither covers the sibling runtime packages. `@lattice-php/core` owns the responsive-visibility wrapper
(`hidden md:contents`), so without the `@source '../../node_modules/@lattice-php/*/dist'` glob in `app.css`,
`visibleFrom()`/`hiddenFrom()` compile to a class that does not exist and hide the node at every width. Keep the glob,
and suspect it first when a Lattice utility class silently does nothing.

## Lattice owns the design tokens

`@import '@lattice-php/lattice/css'` pulls in the token layer. Override a token unlayered on `:root` so it wins
regardless of import order; do not restate Lattice's own defaults.
