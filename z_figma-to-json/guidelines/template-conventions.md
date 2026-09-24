# Team conventions — learned from the reference templates

Source: the real Bricks exports in `guildelines-references/` (made on `test-brisks.test`):

| File | Page | Elements |
| --- | --- | --- |
| `template-single-home-2026-09-24.json` | Home | 229 |
| `template-single-services-2026-09-24.json` | Services | 161 |
| `template-single-about-2026-09-24.json` | About | 113 |

They show **how the team builds pages** — structure, responsive scaling, sizes. They are *not* a
source of setting keys: they contain a lot of stale and Pro-only data (see §6). Use them for
patterns; take every key/value from `reference/controls-free.json`.

## 1. Structure

- `section` (full width, side gutter 20 / 15) → `container` (content width) → nested `container`s
  as rows/columns → content elements. Columns are **containers with % widths** (e.g. 4 cards:
  `_width: 23.5%` → `laptop 23%` → `tablet_portrait 48%` → `mobile_portrait 100%`), and `block`/`div`
  for smaller groupings.
- Content container widths come from Figma: `1720px` (1920 − 2×100) and `1290px` are common, then
  `100%` at smaller breakpoints.
- Key parts get a builder `label` ("Hero", "Work Section", "Card 1", "leftside"/"rightside").
- Native elements used: heading, text / text-basic, image, icon, icon-box, list, divider, button,
  video, form, rating, slider-nested (+ post-title/post-meta for dynamic content).
- Headings are mostly `tag: "h3"` in the templates even for section titles — for new pages use a
  correct outline (one H1, H2 per section, H3 inside) unless told otherwise.

## 2. Responsive scaling seen in practice — background only

> **Not rules.** Responsive values come only from `Design Layout.xlsx` (desktop = Figma, smaller =
> sheet) — see `responsive-guidelines.md`. This section just records what the old pages did.

Breakpoint keys used (count): mobile_portrait 389 · mobile_landscape 353 · mobile 235 ·
tablet_portrait 230 · tablet_landscape 96 · laptop 93 · large_laptop 72 — the guideline set — plus
legacy extras `full`, `desktop_window`, `mac`, `standard_desktop` from older site setups
(**never** used — only the sheet's 8 breakpoints are allowed).

**Section headings (Figma 65–80px)** — matches the sheet's table:

| Figma | laptop | tablet_portrait | mobile_landscape | mobile_portrait | mobile |
| --- | --- | --- | --- | --- | --- |
| 70 | 50 (sometimes) | 46 | 36 | 30 | — |
| 70 | — | 50 | 36 | 42 | 32 |
| 80 | — | 46 | 36 | 30 | — |
| 65 | 50 (large_laptop 60) | 46 | 36 | 36 | 24 |

→ In practice "40 / 36" resolves to **36** and "32 / 30" to **30** for normal section headings.

**Hero / display headings (100–455px)** scale proportionally, not by the table:
`120 → tablet_landscape 100 → tablet_portrait 80 → mobile_landscape 60 → mobile_portrait 40`;
`455 → 350 → 312 → 261 → 220 → 192 → 80`.

**Small headings** get gentle steps (not in the sheet):
`30 → tablet_portrait 24 → mobile_landscape 22` · `28 → 20` · `24 → 20 → 18` · `20 → 18`.

**Body / lead text:** `18 → 16` (at mobile_landscape or mobile_portrait); lead `30 → 24 → 20`;
`28 → tablet_portrait 22 → mobile_portrait 16`. Text at 16px stays 16.

**Section padding:** desktop = Figma (e.g. 105/136, 130/230, 141/220, 154/163), then roughly
`tablet_portrait 100` → `mobile_landscape 80` → `mobile_portrait 60`, side gutter `20 → 15` at
mobile_portrait. `large_laptop`/`laptop` values are often slightly-reduced Figma values
(99, 118, 123) rather than exactly 120. Bottom padding is sometimes left larger than the table.

**Sliders (`slider-nested`):** `perPage 3 → tablet_portrait 2 → mobile 1`, `gap 25 → 20`, height
per breakpoint (`vh` on mobile), `autoHeight: true`.

## 3. Typography & colour

- Fonts vary per template (Kanit, Instrument Sans, Teko, Plus Jakarta Sans) → always per design.
- Templates use site CSS variables (`var(--h1-font-color)`, `var(--body-txt-color)`, …) and palette
  references (`{"raw":"var(--bricks-color-grey-100)","id":"…","light":"…"}`). Those only exist on
  the source site → **generated JSON uses plain hex** (`{"raw":"#18181b"}`).

## 4. Bricksfly in the templates (and what is still allowed)

| Seen in templates | Tier | Allowed now? |
| --- | --- | --- |
| `aab-button-pro` (Button Pro widget, styles `base-*` / `pro-1…8`) | Free | ✅ |
| `aab-brand-slider` | Free | ✅ |
| `aab_animation: fade/move` on containers, images, text (Advanced Animation) | **Pro** | ❌ |
| `aab_text_animation_type: word/text_reveal/text_move/char/text_invert` | **Pro** | ❌ |
| `aab_enable_pin_area` (Pin), `aab_image_animation` (Image Animation) | **Pro** | ❌ |
| tilt, tooltip, wrapper link, cursor (`zz3_che5x_*`), mouse move, horizontal scroll | **Pro** | ❌ |

Free replacement for entrance animations: **Starter Animations** — `_bricksfly_starter_anim`
(heading, text, text-basic, text-link, image: `reveal | scale-up | slide | skew-reveal | flip |
text-glow | text-typewriter | text-mask-wipe | text-wave | text-bg-clip | text-char-animate`) and
`_bricksfly_starter_anim_container` (section, container, block, div: `slide | flip`).
Still proposed in the mapping table and only added after approval.

## 5. Button Pro quick reference (free)

`btnStyle` (`base-default|base-square|base-underline|base-mask|base-oval|base-circle|base-ellipse|pro-1…pro-8`),
`btnHoverVariant` (`hover-none|hover-divide|hover-cross|hover-cropping|rollover-top|rollover-left|parallal-border|rollover-cross`),
`btnText`, `btnIcon`, `btnIconPosition`, `btnLink`, `btnTypo`, `btnBg`, `btnColor`, `btnHColor`,
`btnBorder`, `btnHBorder`, `btnPadding`, `btnGap`… (`node tools/lookup.mjs aab-button-pro`).
Use it when a design button has an animated hover/icon style; a plain button → Bricks `button`.

## 6. Never copy from the templates

- Stale Bricksfly key generations: `_aab_*`, `_thebrbre_*` (now `_bricksfly_*`), `aab_tilt_speed`,
  `wealcoder-aab:*`, tooltip `arrow: true` / string colours (old formats).
- `blc_live_copy` (another plugin), `btnStyle: "3"` (old value).
- Legacy breakpoint keys `full`, `mac`, `desktop_window`, `standard_desktop`.
- Media-library images (`id` + `test-brisks.test` URLs), palette colours, site CSS variables.
- The ~100 default `aab_*` values every element carries (Pro, and defaults anyway).
