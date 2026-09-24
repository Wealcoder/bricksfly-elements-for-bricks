# z_figma-to-json — Figma → Bricks JSON POC

**Start here.** This folder holds everything needed to turn a Figma design (PNG/screenshot)
into an importable Bricks template JSON built from **Bricks native + Bricksfly FREE** features.
A new session only needs: *"read z_figma-to-json/README.md, here is the design: <file>"*.

---

## 1. What we are proving

```text
Figma screenshot → analyze design → choose Bricks native / Bricksfly features
→ generate complete Bricks JSON → validate → import in Bricks → editable page
```

No MCP server yet. Full original brief: [`Bricks + Bricksfly Figma-to-JSON.txt`](Bricks%20+%20Bricksfly%20Figma-to-JSON.txt).

## 2. Rules (agreed, apply every time)

| # | Rule |
| --- | --- |
| 1 | Priority: **Bricks native → existing Bricksfly FREE element/extension → ask before custom JS**. Never create new elements/extensions. |
| 2 | Never invent element names, control keys or values. Look them up in `reference/controls-free.json` (`tools/lookup.mjs`). |
| 3 | No custom JS / custom CSS / custom HTML automatically. If something can't be built with Bricks/Bricksfly: stop and ask. |
| 4 | Don't overuse Bricksfly — a plain heading is a Bricks Heading. |
| 5 | **Animations:** screenshots show no motion → *propose* free Starter Animations in the mapping table, add them to JSON **only after approval**. |
| 6 | **Images:** external placeholder URLs (`https://placehold.co/WxH/png?text=…`, `"external": true`). |
| 7 | **One page = one template JSON** (`templateType: "content"`); header/footer are normal sections. |
| 8 | **Bricksfly FREE only — never use any Pro (paid) widget, extension or setting.** See §2a. The validator rejects Pro by default. |
| 9 | **Responsive = `Design Layout.xlsx` only:** desktop = Figma measurements, every smaller breakpoint = the sheet. Rules in [`guidelines/responsive-guidelines.md`](guidelines/responsive-guidelines.md). Structure patterns (not responsive values) in [`guidelines/template-conventions.md`](guidelines/template-conventions.md). |
| 10 | Nothing is handed over until validator = 0 errors, render test passes, and screenshots were compared with the design. |

### 2a. Free vs Pro (from `config.php` + a Pro-disabled controls dump)

| ✅ Free — allowed | ❌ Pro — never |
| --- | --- |
| **Widgets:** Button Pro (`aab-button-pro`, free despite the name), Social Icons, Brand Slider, Counter, Progress Bar, Icon Box, Testimonial 1/2/3, Team, Timeline, Floating Elements, Animated Heading, Post Meta, Video Story, Video Box, Video Mask, Video Box Slider, Image Accordion, Toggle Switch, Draggable Items | **Widgets:** Gallery Progress, Animated Off-Canvas, Social Share, Video Popup, YouTube Videos |
| **Extension:** Starter Animations — `_bricksfly_starter_anim*` on heading/text/text-basic/text-link/image and `_bricksfly_starter_anim_container` on section/container/block/div | **Every catalog extension:** Advanced Animation (`aab_animation`), Text Animation (`aab_text_*`), Image Animation, Pin, Parallax, Tilt, Tooltip, Wrapper Link, Cursor Hover/Move (`zz3_che5x_*`, `aab_mouse_*`), Image Reveal on Hover, Horizontal Scroll, ScrollTo |
| | **Site settings:** Smooth Scroller (ScrollSmoother), Preloader, Cursor, Scroll Indicator, Scroll To Top |

All Bricks native elements are allowed. Anything not in the free column → Bricks native, or ask.

### Responsive summary — desktop = Figma, smaller = sheet (details in the guideline file)

| Key | Max width | Section padding T/B, L/R | Heading | Body |
| --- | --- | --- | --- | --- |
| desktop (base, no suffix) | 1920 | Figma | Figma | Figma |
| `large_laptop` | 1440 | 120, 20 | Figma | Figma |
| `laptop` | 1366 | 120, 20 | 50 | Figma |
| `tablet_landscape` | 1200 | 120, 20 | 50 | Figma |
| `tablet_portrait` | 1024 | 100, 20 | 46 | Figma |
| `mobile_landscape` | 880 | 80, 20 | 40 / 36 | Figma |
| `mobile_portrait` | **767** | 60, 15 | 32 / 30 | 16 |
| `mobile` | 576 | 60, 15 | (inherits) | 16 |

Only these 8 breakpoints — nothing outside the sheet. Only ever *reduce* sizes (a 24px card title
or 14px caption keeps its Figma size).
**The import site must have exactly these breakpoint keys and widths** (Bricks → Settings → Breakpoints) —
`test-brisks.test` has them (but Mobile Portrait is 768 there → set 767); `bricks-animation-dev.test` does **not**.

### Open questions (ask the user if still unanswered)

1. **Preview/import site:** the dev site `bricks-animation-dev.test` lacks the sheet's breakpoints. Either preview on `test-brisks.test` (has them; Bricksfly free 1.0.0 there, Mobile Portrait 768 → 767) or reconfigure the dev site's breakpoints (affects its 87 existing Bricks pages). Ask before changing any site's settings.
2. **Heading "40 / 36" and "32 / 30":** assumed first value = primary heading (H1 / hero / main section H2), second = other large headings.
3. **Header/footer bars** whose Figma padding is far below the section table (e.g. 16px): assumed they keep Figma padding — flag in Notes.

Decided (2026-09-24): breakpoints = the sheet's 8 only · desktop = Figma, smaller = sheet · Mobile Portrait 767px · nothing outside the sheet (no `mac`/`full`/`desktop_window`/`standard_desktop`).

## 3. Folder map

| Path | What |
| --- | --- |
| `README.md` | This file — entry point. |
| `Bricks + Bricksfly Figma-to-JSON.txt` | Original brief (rules, output format A–E). |
| `guidelines/responsive-guidelines.md` | Responsive rules (breakpoints, padding, font sizes, checklist). |
| `guidelines/template-conventions.md` | How the team builds pages (structure, real responsive scaling, Button Pro, what never to copy) — learned from the reference templates. |
| `guildelines-references/Design Layout.xlsx` | Team source sheet for the responsive rules. |
| `guildelines-references/template-single-{home,services,about}-*.json` | Real team pages exported from `test-brisks.test` — **patterns only**, full of stale and Pro keys. |
| `reference/controls-free.json` | **Source of truth**: every element available with Bricks 2.1.4 + Bricksfly **Free** (95) and its runtime controls, types, options, defaults. Generated with Pro disabled in memory. |
| `reference/controls.json` | Same with Pro active (100 elements) — only used to tell "this is Pro" apart from "this does not exist". |
| `tools/` | Dump / lookup / validate / render / preview tools (see §5 and `tools/README.md`). |
| `designs/<name>/build.mjs` | One generator script per design; writes `output/<name>-page.json`. |
| `output/` | Final import files. |
| `tests/` | Validator self-test samples (one valid, one deliberately broken). |
| `template-single-page-01-2026-09-24.json` | Real Bricks export from `test-brisks.test` — **format reference only**. It predates the Bricksfly `_aab_*` → `_bricksfly_*` starter-animation rename and has stale keys; don't copy settings from it. |
| `faq.png` | Design #1 (done → `output/faq-page.json`). |

## 4. Workflow for a new design

1. **Put the design** in this folder (e.g. `pricing.png`).
2. **Analyze** (read the image; sample exact colors/measurements with Python PIL — see §7).
3. **Map** each part → element. Check the element/controls exist:
   `node tools/lookup.mjs <element> [keyFilter]` (free tier), and follow `guidelines/template-conventions.md` for structure/scaling. For nestable elements copy the structure Bricks
   itself creates (`get_nestable_children()` in `wp-content/themes/bricks/includes/elements/<name>.php`),
   including the required `_hidden._cssClasses`.
4. **Write** `designs/<name>/build.mjs` (copy `designs/faq/build.mjs` as the template: tokens, `el()` helper,
   deterministic 6-char IDs, parent-first ordering, wrapper). Run `node designs/<name>/build.mjs`.
5. **Validate:** `node tools/validate.mjs output/<name>-page.json` → must be **0 errors**.
6. **Render test:** `php tools/render-test.php output/<name>-page.json` → all elements in HTML.
7. **Screenshot & compare** (§6). Fix, rebuild, repeat.
8. **Delete** the preview page (`php tools/preview-page.php delete <slug>`).
9. **Report** in the brief's format: A. design analysis tree · B. mapping table (+ proposed animations) ·
   C. unsupported features (stop & ask if custom JS needed) · D. JSON (file path — files are too large to paste) ·
   E. notes (inferred values, fonts to confirm, placeholder assets, limitations).

## 5. Tools (run from this folder)

```bash
PHP=/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe

$PHP tools/dump-controls.php --free > reference/controls-free.json   # refresh after plugin/Bricks changes
$PHP tools/dump-controls.php > reference/controls.json                # with Pro, for Pro detection
node tools/lookup.mjs                                     # list all 95 free-tier elements
node tools/lookup.mjs accordion-nested title              # controls matching "title"
node tools/lookup.mjs heading tag --full                  # full control definition
node tools/validate.mjs output/x.json                     # free tier + guideline breakpoints (defaults)
node tools/validate.mjs output/x.json --breakpoints=standard|site --tier=pro
$PHP tools/render-test.php output/x.json [--html]         # in-memory render, no DB writes
$PHP tools/preview-page.php create output/x.json zz-figma-poc-x   # temp page → prints URL
$PHP tools/preview-page.php delete zz-figma-poc-x
```

The PHP tools boot the WordPress install three levels up (`bricks-animation-dev`), run as the
first administrator, and set `REQUEST_SCHEME` to avoid wp-config warnings.

## 6. Screenshots

```bash
CH="/c/Program Files/Google/Chrome/Application/chrome.exe"
"$CH" --headless=new --disable-gpu --hide-scrollbars --window-size=1920,1760 \
      --virtual-time-budget=10000 --screenshot=desktop.png "$URL"
```

- Headless Chrome on Windows won't lay out narrower than ~500px. For mobile, screenshot an HTML file
  containing `<iframe src="$URL" width="390" height="3000">` at `--window-size=500,3000`, then crop to 390.
- Put screenshots in the session scratchpad, not in this folder.
- Compare side by side with the design (PIL: paste both into one image).

## 7. Environment facts

| Thing | Value |
| --- | --- |
| Dev site | `http://bricks-animation-dev.test` — DB `bricks-animation-dev`, root/no password, prefix `wp_` |
| Guideline-breakpoint site | `http://test-brisks.test` — DB `test-brisks` |
| Bricks | 2.1.4 (`wp-content/themes/bricks`) |
| Bricksfly | free 1.0.2 + Pro 1.0.3 on dev site (free 1.0.0 on test-brisks) |
| PHP | `/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe` |
| MySQL client | `/c/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysql.exe -uroot <db>` |
| Python | PIL available (`python`), no openpyxl — read `.xlsx` by unzipping the XML |

## 8. Bricks JSON format (verified)

**Import wrapper** (Bricks → Templates → Import; tick "Import images" to keep external URLs):

```json
{ "title": "…", "type": "content", "templateType": "content", "tags": [], "bundles": [], "content": [ … ] }
```

`tags` and `bundles` must be arrays (importer reads them with `is_array`).

**Element:** `{ "id": "a1b2c3", "name": "heading", "parent": 0 | "<id>", "children": ["<id>", …], "settings": {…}, "label": "optional" }`
— id = 6 lowercase alphanumerics, parent-first order, root elements are `section`s.

**Settings:**

| What | Format |
| --- | --- |
| Responsive / state | `key:breakpoint:pseudo` e.g. `_padding:tablet_portrait`, `_background:hover` |
| Color | `{ "raw": "#18181b" }` |
| Spacing | `{ "top": "20", "right": "20", "bottom": "20", "left": "20" }` (strings, px implied) |
| Typography | `_typography: { "font-family", "font-weight", "font-size", "line-height", "letter-spacing", "color": {raw}, "text-align" }` |
| Border | `{ "width": {sides}, "style": "solid", "color": {raw}, "radius": {sides} }` |
| Image | `{ "url": "…", "external": true, "filename": "x.png" }` |
| Icon | `{ "library": "ionicons", "icon": "ion-md-add" }` (Ionicons, Font Awesome 6, Themify bundled) |
| Link | `{ "type": "external", "url": "#" }` |
| Hidden required classes | `"_hidden": { "_cssClasses": "tab-menu" }` (not a control; used by nestable elements) |

Bricksfly extension defaults are filled in at render time → **only write settings that differ from defaults**.

## 9. Lessons learned (don't rediscover)

- **Tabs (Nestable)** `tabs-nested`: `direction: "row"` = menu left, content right. Titles are
  `width:auto` → set `titleWidth: "100%"`. No gap control → use `_margin` on the tab-menu block.
  Required classes: `tab-menu`, `tab-title`, `tab-content`, `tab-pane`.
- **Accordion (Nestable)** `accordion-nested`: `expandItem` is **0-based** text (`"1"` = 2nd item),
  `faqSchema: true` for FAQ JSON-LD. +/− swap = two `icon` children with `isAccordionIcon: true` and
  `accordionTitleIconState: "collapsed"` / `"expanded"`. Put `_flexWrap: "nowrap"` on the title block
  and `_flexShrink: "0"` on icons or they wrap on mobile. Classes: `accordion-title-wrapper`, `accordion-content-wrapper`.
- **Nav (Nestable)** `nav-nested`: block `tag: "ul"` + class `brx-nav-nested-items` holding `text-link`s
  and a close `toggle` (class `brx-toggle-div`), plus an open `toggle`. `mobileMenu` = breakpoint key for the hamburger.
- **Button:** `circle: true` = pill. No `style` → style freely with `_background`/`_typography`.
- Control `default`s in Bricks are applied only when an element is added in the builder, not on import —
  set explicitly anything that matters.
- **Bricksfly starter animations** keys are `_bricksfly_starter_anim` (text/media) and
  `_bricksfly_starter_anim_container` (section/container/block/div). Old `_aab_*` names are dead.
- **Pro is forbidden.** The team templates lean on Pro (`aab_animation: fade`, `aab_text_animation_type`, pin) — replace with free Starter Animations (after approval) or nothing.
- **Smooth Scroller, preloader, cursor, scroll indicator, scroll-to-top** are Bricksfly **Pro site settings** — not available (free only) and never part of page JSON.
- Bricks blocks writing `_bricks_page_content_2` unless the current user can use the builder
  (preview tool logs in as admin for that).
- `generate_css_from_elements()` doesn't return CSS — read `\Bricks\Assets::$inline_css['content']`.
- Font identification from screenshots is a guess → always flag fonts for confirmation. Wider
  substitute fonts change line breaks; compensate with heading `_widthMax`, not by shrinking font sizes.

## 10. Design log

| Design | Generator | Output | Status |
| --- | --- | --- | --- |
| `faq.png` (header, FAQ tabs + accordion, CTA) | `designs/faq/build.mjs` | `output/faq-page.json` (263 elements) | Bricks native only (passes free tier). Built with Bricks default breakpoints; **needs rebuild to the responsive guideline**. Pending: font confirmation (used Source Serif 4 + Figtree), Starter Animation proposals. |
