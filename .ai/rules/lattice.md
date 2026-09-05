---
paths:
    - "app/Actions/**"
    - "app/Forms/**"
    - "app/Tables/**"
    - "app/Fragments/**"
    - "app/Pages/**"
    - "app/Layouts/**"
---

# Lattice definitions

## Read context, never route parameters or session state

A definition runs twice: once while the page renders, and again on its own signed endpoint, which carries no route
parameters. Read records with `$this->contextModel('team')` (aborts 404 when the key is absent or resolves to
nothing) or `$this->contextModelOrNull('team')` (returns null, for render-time gates). Never reach for
`$request->route(...)` inside a definition.

The keys are registered once in `AppServiceProvider::registerLatticeContext()`. A key with a resolver cascades into
every child component Lattice builds, so a page that types `Team $team` in `render()` already seeds `team` for the
forms, tables, and actions on it — do not pass `['team' => $team->slug]` by hand.

## Declare authorization on the attribute

Put the ability and its subject on the definition attribute rather than in an `authorize()` body:

```php
#[AsForm('teams.update', can: 'update', on: 'team')]
#[AsAction('teams.members.remove', can: 'removeMember', on: 'team')]
```

`on` names a registered context key; Lattice resolves the subject before the definition runs and denies a missing
one. Keep `authorize()` only for conditions a gate subject cannot express. A plain component takes the same pair as
a method: `Heading::make(…)->can('update', on: 'team')`.

A page's `can` gates who may load the page; it does **not** gate the definitions rendered on it. Every definition
needs its own declaration.

## Page middleware merges with the config default

`config('lattice.pages.middleware')` is `['web']` and attribute middleware is appended, never substituted. Write
`middleware: ['auth', 'verified']`, not `['web', 'auth', 'verified']`.

## handle() takes validated data

Lattice validates a form or action form before `handle()` runs and passes the result in. Declare
`handle(FormData $data)` and read through `$data->string('name')`, `$data->enum('role', TeamRole::class)`, and the
rest of the `ValidatedInput` API. Never call `$this->validate($request)` yourself, and never read raw request input
for a field the schema declares.

## A table calls actions() once per row

An unmemoized `$user->can(...)` in `actions()` is a query per row. `App\Tables\Concerns\AuthorizesRowActions` caches
the answer on the definition instance, which lives exactly one render — the window in which it cannot change. Reach
for it rather than a cache on the model, where a later `attach()` would leave it stale.

## An empty ActionGroup still renders

Lattice drops an unauthorized row action, but the `ActionGroup` wrapping it is a plain container and would render as
an empty dropdown. Build the action list first and return `[]` from `actions()` when nothing survives.
