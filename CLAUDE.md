# schema-mapper

PHP library for mapping external API data to typed objects (`yeremi/schema-mapper`). Used by `wp-ingresso`.

## Commands
- `composer test` — PHPUnit
- `composer phpstan` — PHPStan level 10 (uses `phpstan-baseline.neon`)
- `composer phpstan-baseline` — regenerate the baseline (only when I ask; don't add entries to hide new errors)
- `composer cs-check` / `composer cs-fix` — php-cs-fixer
- `composer rector-check` / `composer rector-fix` — Rector
Releases are automated by semantic-release — don't edit versions or `CHANGELOG.md` by hand.
