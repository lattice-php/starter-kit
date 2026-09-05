---
paths:
    - "app/Providers/**"
    - "app/Models/**"
---

# Context resolvers

## One place registers them

`AppServiceProvider::registerLatticeContext()` is the only place `Lattice::context()` is called. A key registered
there resolves at most once per request and cascades into every child component, so adding a key is how you make a
record reachable from a definition.

## Dependent keys resolve inside their parent

`member`, `invitation`, and `passkey` resolve through the record that owns them — the team, or the signed-in user —
rather than by a bare `findOrFail()` on the id. That scoping is what makes a forged id in a sealed reference a 404
instead of another team's record. Keep it when adding a key.

## Give a closure resolver a concrete return type

Lattice reads the declared return type to map a bound route model to its context key, which is what lets
`render(PageSchema $schema, Team $team)` seed the `team` frame. A resolver without one needs an explicit
`model: Team::class`.

## Model docblocks are generated

The `@property` blocks come from `barryvdh/laravel-ide-helper`. Where the generator is wrong about nullability — a
`NOT NULL` foreign key it types as `|null` — correct it in place rather than working around it at every call site.
