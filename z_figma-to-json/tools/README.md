# Figma → Bricks JSON tooling

All commands run from `z_figma-to-json/`. PHP: `/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe`.

| Step | Command | What it does |
| --- | --- | --- |
| 1. Refresh catalog | `php tools/dump-controls.php --free > reference/controls-free.json` and `php tools/dump-controls.php > reference/controls.json` | Free-tier catalog (Pro disabled in memory) is the source of truth; the full one is only for detecting Pro. Boots WordPress and dumps every registered element (Bricks native + Bricksfly free/Pro) with the controls Bricks sees at runtime, including extension controls injected into native elements. Re-run after changing either plugin. |
| 2. Look up controls | `node tools/lookup.mjs [element] [keyFilter] [--full] [--pro]` | Lists elements, or one element's control keys, types, options and defaults. |
| 3. Validate | `node tools/validate.mjs <file.json>` | Checks the import wrapper, IDs, parent/child links, element names, setting keys vs. registered controls, guideline breakpoints (`--breakpoints=standard|site` to switch), free tier only (`--tier=pro` to allow Pro), and value shapes (select options, colors, spacing, typography…). Exit 1 on errors. |
| 4. Render test | `php tools/render-test.php <file.json> [--html]` | Renders the elements through Bricks' own renderer in memory (no DB writes) and prints the generated CSS/HTML. |

Breakpoints: follow `../guidelines/responsive-guidelines.md` (desktop base + large_laptop, laptop, tablet_landscape, tablet_portrait, mobile_landscape, mobile_portrait, mobile). The import site must have those breakpoint keys configured.

Import in Bricks: **Templates → Import**, tick "Import images" to keep external image URLs (unticked, Bricks swaps them for placeholders).
