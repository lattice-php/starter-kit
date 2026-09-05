---
paths:
    - "lang/**"
---

# Translations

## Lowercase keys only

Key segments use lowercase letters, numbers, and dashes. Never camelCase — `teams.invite.already-member`, not
`teams.invite.alreadyMember`.

## Dot notation via nested arrays

Use nested PHP arrays to build dot-separated keys: `'invite' => ['submit' => '...']` resolves to
`teams.invite.submit`. Dots express hierarchy; keep dashes for compound terms (`email-address`, `recovery-codes`).

## Suffixes for secondary strings

`.label` for a form label that also has helper text, `.help-text` for the helper text, `.title`/`.body` for a
notification that has both.

## common.* for reusable strings

Shared field labels (`common.field.email-address`), actions (`common.action.save`), and statuses live in
`lang/{locale}/common.php`.

## File naming

Translation files use kebab-case filenames matching the area (`teams.php`, `settings.php`, `navigation.php`).

## Always update both locales

Every addition or change lands in `lang/en/` **and** `lang/de/`.
