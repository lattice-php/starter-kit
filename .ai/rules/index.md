# Project Rules Index

Before planning or editing, find the row whose globs match the file's path and read that rule file.

Every glob is a code span. A bare `*` in a table cell reads as markdown emphasis, and a formatter will rewrite
`app/*/Concerns/**` into `app/_/Concerns/\**` — silently breaking the mapping this file exists to carry.

| Applies to | Rule file |
| --- | --- |
| `app/Actions/**`, `app/Forms/**`, `app/Tables/**`, `app/Fragments/**`, `app/Pages/**`, `app/Layouts/**` | .ai/rules/lattice.md |
| `app/Providers/**`, `app/Models/**` | .ai/rules/context.md |
| `resources/js/**`, `resources/css/**`, `vite.config.ts` | .ai/rules/frontend.md |
| `tests/**` | .ai/rules/testing.md |
| `lang/**` | .ai/rules/translations.md |
