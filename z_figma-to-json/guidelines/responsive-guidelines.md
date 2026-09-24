# Responsive guidelines — Figma → Bricks JSON

Source: **`guildelines-references/Design Layout.xlsx` only** (user decision, 2026-09-24).
Nothing outside that sheet is used for responsive values — not the reference templates, not Bricks
defaults, not other breakpoints. `tools/validate.mjs` enforces the breakpoint keys (default mode).

The rule in one line: **Desktop = Figma measurements. Every smaller breakpoint = the sheet.**

## 1. Breakpoints (only these 8)

Bricks is desktop-first: each breakpoint is a `max-width`; a setting without suffix is the desktop
value; responsive values are stored as `setting:breakpoint_key`.

| Key (setting suffix) | Max width | Name (sheet) |
| --- | --- | --- |
| *(none)* — `desktop`, base | 1920 | Desktop (Figma) |
| `large_laptop` | 1440 | Large Laptop |
| `laptop` | 1366 | Laptop |
| `tablet_landscape` | 1200 | Tablet Landscape |
| `tablet_portrait` | 1024 | Tablet Portrait |
| `mobile_landscape` | 880 | Mobile Landscape |
| `mobile_portrait` | **767** | Mobile Portrait |
| `mobile` | 576 | Mobile |

- Mobile Portrait = **767px** (user decision; measured as in Chrome). The sheet also says 768 in one
  place — 767 wins. `test-brisks.test` is currently set to 768 → change it to 767 there.
- **No other keys, ever** (no `mac`, `full`, `desktop_window`, `standard_desktop`, `dt_*`, …).
- The site the JSON is imported into must have exactly these keys and widths configured
  (Bricks → Settings → Breakpoints); settings for keys a site doesn't have are silently ignored.
  `bricks-animation-dev.test` currently has a different set.
- Keys are the ones already used on `test-brisks.test` (the sheet gives names/widths, not keys).

## 2. Spacing — padding & margin

Desktop: **Figma measurements** for every padding, margin and gap.
Smaller breakpoints: **the sheet** — section padding:

| Breakpoint | Top / Bottom | Left / Right |
| --- | --- | --- |
| Desktop (base) | Figma | Figma (20 in the sheet) |
| `large_laptop` (≤1440) | 120 | 20 |
| `laptop` (≤1366) | 120 | 20 |
| `tablet_landscape` (≤1200) | 120 | 20 |
| `tablet_portrait` (≤1024) | 100 | 20 |
| `mobile_landscape` (≤880) | 80 | 20 |
| `mobile_portrait` (≤767) | 60 | 15 |
| `mobile` (≤576) | 60 | 15 |

Written on every **section** (only emit a breakpoint where the value changes — Bricks inherits
downward):

```json
"_padding": { "top": "<figma>", "right": "<figma>", "bottom": "<figma>", "left": "<figma>" },
"_padding:large_laptop": { "top": "120", "right": "20", "bottom": "120", "left": "20" },
"_padding:tablet_portrait": { "top": "100", "bottom": "100" },
"_padding:mobile_landscape": { "top": "80", "bottom": "80" },
"_padding:mobile_portrait": { "top": "60", "right": "15", "bottom": "60", "left": "15" }
```

- `laptop`, `tablet_landscape` and `mobile` need no key of their own (same as the row above).
- Other spacing (margins, gaps, inner padding) the sheet doesn't list: desktop = Figma; on smaller
  breakpoints only change it where the layout needs it (e.g. stacked columns) and say so in Notes.
- **Interpretation (flag in Notes when used):** the table is section spacing. A header/footer *bar*
  whose Figma padding is far below the table (e.g. 16px) keeps its Figma padding — applying
  120/100/80 to a menu bar would break it.

## 3. Heading font sizes

Desktop = Figma. Smaller breakpoints = sheet (cell comment: "Depend on figma and desktop, After
Desktop").

| Breakpoint | Size (px) |
| --- | --- |
| Desktop (base) | Figma |
| `large_laptop` (≤1440) | Figma (no row in the sheet) |
| `laptop` (≤1366) | 50 |
| `tablet_landscape` (≤1200) | 50 |
| `tablet_portrait` (≤1024) | 46 |
| `mobile_landscape` (≤880) | 40 / 36 |
| `mobile_portrait` (≤767) | 32 / 30 |
| `mobile` (≤576) | inherits `mobile_portrait` (no row in the sheet) |

- Written as `_typography:<key>: { "font-size": "<n>" }`.
- Only **reduce**: a heading whose Figma size is already ≤ a breakpoint's value keeps its size from
  there on (a 24px card title never becomes 50). Applies to all headings larger than the table,
  including hero headings.
- **"40 / 36" and "32 / 30" — still to confirm.** Current assumption: first value for the primary
  heading of the page/section (H1, hero, main section H2), second value for other large headings.
- Use unitless / `em` line-heights so they scale; a px line-height needs its own overrides.

## 4. Body font sizes

| Breakpoint | Size (px) |
| --- | --- |
| Desktop → `mobile_landscape` | Figma |
| `mobile_portrait` (≤767) and `mobile` | 16 |

- Written as `_typography:mobile_portrait: { "font-size": "16" }` on body text (text-basic, text,
  and accordion/tab content typography controls).
- Only reduce: text ≤16px in Figma keeps its size.

## 5. Layout changes

Not in the sheet → design-driven, kept minimal, always listed in Notes as *inferred*
(e.g. two columns stacking at `tablet_portrait`, nav switching to the hamburger).

## 6. Not applied

The sheet's custom CSS snippets (`@media 1367–1540`, `1441–1780 width 79.5%`, slider autoplay CSS,
focus-visible outline, menu paddings), `document.designMode`, and the "Need To Check" site checklist
are **not** added to generated JSON without explicit approval (no custom CSS/JS by default).

## 7. Checklist before handing over a JSON

1. Only §1 keys as setting suffixes → `node tools/validate.mjs <file>` = 0 errors.
2. Desktop values = Figma measurements.
3. Every section has the §2 padding overrides (or the flagged header/footer-bar exception).
4. Every heading larger than the table has the §3 overrides; smaller ones untouched.
5. Body text >16px has the `mobile_portrait` 16px override.
6. Preview on a site with the §1 breakpoints; screenshots compared with the design.
