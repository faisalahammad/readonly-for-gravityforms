# Readonly for Gravity Forms — Claude Code Project Brief

## What This Plugin Does
A WordPress plugin that adds a "Make this field read-only" option to Gravity Forms fields.
When enabled, the field renders as read-only on the frontend (and/or admin, depending on
context) without removing its submitted value or breaking Gravity Forms' own validation/
rendering pipeline.

---

## Project Structure

```
readonly-for-gravityforms/
├── readonly-for-gravityforms.php     # Main plugin file (headers + bootstrap only)
├── README.txt                        # WordPress.org readme
├── LICENSE
├── composer.json                     # PHPCS/WPCS/PHPUnit dev dependencies + build/zip/test scripts
├── phpcs.xml.dist                    # WPCS ruleset config
├── phpunit.xml.dist                  # PHPUnit config
├── tests/                            # DB-free PHPUnit unit tests (bootstrap stubs WP/GF functions)
├── .distignore                       # Dist exclusion rules for build-zip.php
├── build-zip.php                     # Build distribution zip from .distignore rules
├── includes/
│   ├── class-rfgf-loader.php         # Hook registration (central place all add_action/add_filter are wired)
│   ├── class-rfgf-field-settings.php # gform_field_standard_settings — admin field-settings checkbox UI
│   ├── class-rfgf-tooltips.php       # gform_tooltips registration
│   ├── class-rfgf-field-render.php   # gform_field_content — readonly enforcement logic
│   └── class-rfgf-assets.php         # Admin CSS/JS enqueue, gated by is-GF-admin-page check
├── assets/
│   ├── css/
│   │   └── readonly-for-gravityforms.css
│   └── js/
│       └── readonly-for-gravityforms-admin.js
├── languages/
│   └── readonly-for-gravityforms.pot
└── index.php                         # Silence-is-golden stub
```

Note: current codebase is the class-based structure above: thin classes, one hook-area per
class, no deep abstraction. Do not over-engineer beyond what this plugin's actual
size/complexity warrants.

---

## Core Concepts

### Hook Map (verify against actual code before editing — do not assume from names alone)
| Hook | Purpose | Class |
|---|---|---|
| `gform_field_standard_settings` | Injects "Make this field read-only" checkbox in field editor | `RFGF_Field_Settings` |
| `gform_tooltips` | Adds tooltip text for the readonly setting | `RFGF_Tooltips` |
| `gform_field_content` | Renders the field as readonly (disabled/locked) when the setting is on, plus a hidden input copy of the value so it still submits | `RFGF_Field_Render` |
| `gform_enqueue_scripts` | Loads shared frontend style, only for forms with a read-only field | `RFGF_Assets` |
| `admin_enqueue_scripts` | Loads admin CSS/JS, gated to Gravity Forms admin pages only | `RFGF_Assets` |

### Supported Field Types
Authoritative list lives in two places (duplicated by design — frontend PHP vs admin JS):
- PHP: `RFGF_Field_Render::$supported_field_types` (`includes/class-rfgf-field-render.php`)
- JS: `supportedTypes` array (`assets/js/readonly-for-gravityforms-admin.js`)
Keep both in sync when adding/removing types. Do not hardcode a third copy elsewhere.

### Admin Page Detection
Assets must only enqueue on actual Gravity Forms admin screens. Detection is the private
`RFGF_Assets::is_gravityforms_admin_page()` method (`includes/class-rfgf-assets.php`) —
do not re-implement screen checks elsewhere.

---

## Code Style
- WordPress Coding Standards (WPCS) via PHPCS — see `phpcs.xml.dist`
- Tabs, not spaces
- Yoda conditions: `true === $var`
- DocBlocks on every method with `@param` and `@return`
- Class files named `class-rfgf-{name}.php`, one class per file
- Function/hook prefix: `readonly_for_gravityforms_` (legacy procedural) → class methods use
  `RFGF_` class prefix going forward; do not rename existing public-facing hook names/filters
  without explicit approval (back-compat)

---

## PHPCS / WPCS / Composer Setup

- `composer.json` requires `dealerdirect/phpcodesniffer-composer-installer` +
  `wp-coding-standards/wpcs` (+ `phpcompatibility/phpcompatibility-wp` if PHP version range
  checking is wanted)
- `phpcs.xml.dist`: WordPress-Extra ruleset minimum, text-domain check for
  `readonly-for-gravityforms`, PHP compatibility check against the plugin's declared minimum
  PHP version in the plugin header/README.txt
- Run via `composer run phpcs`, `vendor/bin/phpcs`, or `composer run build` (runs phpcs + zip).
  `composer run zip` builds the distribution zip via `build-zip.php`.
- PHPUnit unit tests live in `tests/` and run via `composer run test` (no database needed).
  `tests/bootstrap.php` stubs the few WP/GF functions the classes call (`add_filter`,
  `add_action`, `__`, `rgar`). PHPUnit stays on 9.6 for WP test-lib compatibility. The
  `wp scaffold plugin-tests` integration pattern was not used because the local MySQL
  credentials in wp-config.php do not work.

---

## Constraints & Gotchas

1. **Pure behavior preservation during refactor.** Every hook, filter priority, and rendering
   behavior must survive the file split exactly. No bug fixes, no feature changes bundled into
   a structural refactor unless separately approved.
2. **Unit tests cover rendering and style gating only.** `tests/` covers `RFGF_Field_Render`,
   `RFGF_Tooltips` and frontend style gating in `RFGF_Assets`. Admin UI, admin asset gating and real GF output still need manual QA.
3. Assets must remain gated to Gravity Forms admin screens only — don't let CSS/JS leak onto
   unrelated wp-admin pages.
4. `.pot` file must be regenerated any time translatable strings move between files or change,
   even if the string text itself is unchanged (msgid line references shift).
5. Gravity Forms is a hard dependency for hook targets, but check `class_exists( 'GFForms' )`
   or equivalent before firing GF-specific logic if not already guarded — verify current
   behavior before changing.

---

## Key Files to Read Before Editing
- `includes/class-rfgf-field-render.php` — before touching readonly rendering logic
- `includes/class-rfgf-loader.php` — before adding/moving any hook registration
- Always re-read the file you are about to edit. Do not edit from memory.

---

## Verification After Every Task
Run before reporting done:
```
composer run phpcs
composer run test
coderabbit review --agent
```
Resolve or explicitly justify every finding. Manual QA
against plugin behavior (enable/disable read-only on each supported field type, confirm
frontend renders, confirm value still submits) is the primary safety net.

---

## Git Commits
- **No AI footprint.** Do NOT add `Co-Authored-By: Claude <noreply@anthropic.com>` or any
  AI-attribution trailer to commit messages.
- Use conventional commit prefixes: `fix:`, `feat:`, `refactor:`, `docs:`, `test:`, `chore:`.
- Write human-style commit messages describing what changed and why.
- No em dash anywhere in code comments, commit messages, or PR content.

---

## Current Status
v1.1.1 release prep. PHPUnit unit tests added. See README.txt
changelog for version history.
