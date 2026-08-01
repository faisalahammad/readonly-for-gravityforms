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
├── composer.json                     # PHPCS/WPCS dev dependencies
├── phpcs.xml.dist                    # WPCS ruleset config
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
└── index.php                         # Silence-is-golden stub (present at every directory level)
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
| `gform_field_content` | Renders the field as readonly (disabled/locked) when the setting is on | `RFGF_Field_Render` |
| `admin_enqueue_scripts` (or equivalent) | Loads admin CSS/JS, gated to Gravity Forms admin pages only | `RFGF_Assets` |

### Supported Field Types
See `readonly_for_gravityforms_supported_field_types()` (or its refactored equivalent) for
the authoritative list — do not hardcode a duplicate list elsewhere; reference/reuse it.

### Admin Page Detection
Assets must only enqueue on actual Gravity Forms admin screens. Reuse the existing
`is_gravityforms_admin_page()`-equivalent helper rather than re-implementing screen checks
in multiple places.

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
- Run via `composer run phpcs` (add this script to `composer.json`) or
  `vendor/bin/phpcs`
- No PHPUnit test suite currently exists — if/when added later, follow the same
  `wp scaffold plugin-tests` pattern used in other projects, not part of this initial setup
  unless explicitly requested

---

## Constraints & Gotchas

1. **Pure behavior preservation during refactor.** Every hook, filter priority, and rendering
   behavior must survive the file split exactly. No bug fixes, no feature changes bundled into
   a structural refactor unless separately approved.
2. **No test suite yet.** Verification after any change relies on manual QA + careful code
   review (diff old vs new logic), not automated tests. Be extra careful with sub-agent
   verification steps here since there's no safety net.
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
coderabbit review --agent
```
Resolve or explicitly justify every finding. No PHPUnit suite exists yet, so manual
verification against `TESTING_INSTRUCTIONS.md` is the primary safety net — be thorough in
writing it.

---

## Git Commits
- **No AI footprint.** Do NOT add `Co-Authored-By: Claude <noreply@anthropic.com>` or any
  AI-attribution trailer to commit messages.
- Use conventional commit prefixes: `fix:`, `feat:`, `refactor:`, `docs:`, `test:`, `chore:`.
- Write human-style commit messages describing what changed and why.
- No em dash anywhere in code comments, commit messages, or PR content.

---

## Current Status
Post-refactor baseline. See README.txt changelog for version history.
