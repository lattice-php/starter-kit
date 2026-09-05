---
paths:
    - "tests/**"
---

# Testing

## Pest, with RefreshDatabase applied globally

`tests/Pest.php` binds `Tests\TestCase` and `RefreshDatabase` to the `Feature` and `Browser` suites. Never re-add
`uses(RefreshDatabase::class)` in a test file.

## Prefer feature tests

Exercise the application through HTTP endpoints, Lattice forms, actions and tables, jobs, events, policies, and
database effects. Use unit tests only for complex pure algorithms or small deterministic value objects.

## Lattice UI is exercised via InteractsWithLatticeComponents

`Tests\TestCase` carries the trait: `submitForm(FormClass, $data, $context)`, `callAction(ActionClass, $data,
$context)`, `loadTable(TableClass, $query, $context)`. A definition whose gate denies the actor is **hidden** at
render time, so the normal helpers refuse to seal it — assert the 403 with the `…Denied` variants
(`submitDeniedForm`, `callDeniedAction`, `loadDeniedTable`) instead.

Where a context resolver scopes a lookup, an unauthorized reference is a **404**, not a 403: the record does not
exist for that actor.

## What a test must earn

Every test asserts an observable behavior change caused by an interaction, input, or state transition. Delete on
sight:

- **Render-only tests** — a page loads and shows static text, with no interaction.
- **Styling pins** — assertions on Tailwind utility classes. Assert semantic state instead.
- **Absence-only assertions** — `assertDontSee()` on initial render, unless the same test establishes the positive
  case too.
- **Tautologies** — asserting a factory returns what it was configured with.
- **Duplicated coverage** — every behavior has exactly one owning test. Do not re-assert Lattice's own contract
  (field validation, pagination internals) in every consumer.

## Keep library behavior in its owning library

Generic Lattice or framework behavior belongs in that package's suite, even when the regression first showed up
here. Test this app's configuration, composition, and observable integration behavior.

## tests/ is analysed at PHPStan level 8, with no baseline

Narrow a nullable at the source: prefer `->refresh()` (returns `$this`) over `->fresh()` (returns `?static`), reach
for `findOrFail()`/`firstOrFail()`, and use the `personalTeam()` helper in `tests/Pest.php` where a factory
guarantees the record. Never silence an error with `@phpstan-ignore`, a baseline entry, `assert()`, an inline
`@var`, or a cast.

## Browser tests for client-only behavior

UI behavior that is not about an endpoint's payload goes in `tests/Browser`. They serve the built bundle, so run
`npm run build` first. Target components by their `data-test` attribute — Lattice writes the node's full identity
(`action-teams.3.edit`), or the value of an explicit `->key()`. Adding a stable `key()` to make an assertion clearer
is fine.
