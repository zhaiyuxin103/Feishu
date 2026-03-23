# Repository Guidelines

## Project Structure & Module Organization

`src/` contains the package code under the `Yuxin\Feishu\` namespace, with related concerns split into `Contracts/`, `Enums/`, `Events/`, `Exceptions/`, and `Facades/`. Package configuration lives in `config/feishu.php`. Tests are organized as Pest suites in `tests/Unit` and `tests/Feature`. Use `workbench/` for Laravel Testbench-based manual verification and local package integration; its example environment file is `workbench/.env.example`. Documentation for the public site lives in `docs/`, and CI workflows are in `.github/workflows/`.

## Build, Test, and Development Commands

Use Composer for package work:

- `composer install`: install PHP dependencies.
- `composer test`: purge Testbench state, then run the Pest suite.
- `composer lint`: run Pint formatting and PHPStan analysis.
- `composer test:refactor`: check Rector changes without modifying files.
- `composer refactor`: apply Rector refactors.
- `composer serve`: build and serve the Testbench workbench app.

Use pnpm for docs and git-hook tooling:

- `pnpm install`: install JS dependencies and Husky hooks.
- `pnpm docs:dev`: run the VitePress docs locally.
- `pnpm docs:build`: build the docs site.

## Coding Style & Naming Conventions

Follow PSR-12 with `declare(strict_types=1);` in PHP files. Pint uses the Laravel preset plus strict comparisons, ordered class elements, and imported global namespaces. Match existing naming: classes and enums use PascalCase, methods use camelCase, constants use UPPER_SNAKE_CASE, and test files end with `Test.php`. Keep namespaces aligned with paths under `src/`.

## Testing Guidelines

Write tests with Pest using descriptive `test('...')` blocks. Put isolated logic in `tests/Unit`; use `tests/Feature` for service-provider or Laravel integration behavior. Run `composer test` before opening a PR. CI covers PHP 8.2-8.4 and Laravel 11-12. The existing contributor docs expect strong coverage for new behavior; add or update tests whenever public APIs, events, or config handling change.

## Commit & Pull Request Guidelines

Commits follow Conventional Commits and commonly include scopes, for example `feat(facades): ...` or `fix(messages): ...`. Husky runs `lint-staged` on pre-commit and `commitlint` on commit messages, so keep changes small and messages structured. PRs should include a short summary, linked issue when applicable, test evidence (`composer test`, `composer lint`), and screenshots only when docs or the workbench UI changes.

## Configuration & Security Notes

Do not commit real Feishu credentials. Use environment variables or local workbench config when testing, and keep secrets out of fixtures, docs, and example code.
