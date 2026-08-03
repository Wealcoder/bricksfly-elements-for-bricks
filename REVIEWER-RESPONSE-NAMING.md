# Response: Generic function/class/define/namespace/option names

Thank you for the review. We've gone through each flagged item — a summary
of what we found, below.

## 1. Global functions

Every function this plugin defines at the global scope already uses one of
our two established prefixes, `bricksfly_` or `thebrbre_` (the plugin's
internal option/constant prefix, predating a later public rename). We
re-verified this directly against the current source: there are no
exceptions.

## 2. Classes

Every class in this plugin uses one of two conflict-safe patterns:

- A `BRICKSFLY_` prefix on the class name itself (e.g.
  `BRICKSFLY_Admin`, `BRICKSFLY_Bricks_Brand_Slider`,
  `BRICKSFLY_Template_Importer`), **or**
- A PHP namespace unique to this plugin,
  `wealcoder\thebricksfly\...` (e.g. `Downloader`, `Helpers`,
  `WXRImporter`, `Notices`, `OneClickImport` all resolve to fully-qualified
  names like `wealcoder\thebricksfly\Admin\Base\Downloader`).

A PHP namespace is a standard, WordPress.org-accepted mechanism for
preventing name collisions — it serves the same purpose a prefix does, at
the language level rather than the string level. `Downloader` alone would
be a conflict risk; `wealcoder\thebricksfly\Admin\Base\Downloader` cannot
collide with any other plugin's class regardless of what that plugin names
its own classes, because PHP resolves the fully-qualified name, not the
short name, at load time.

If the automated scan is flagging these namespaced classes, we believe
that's the scanner reading only the literal `class Foo` token without
resolving the enclosing `namespace` statement — a known false-positive
category for this class of tooling. Happy to add an explicit class-name
prefix on top of the namespace as well if that would resolve the flag
definitively; let us know and we'll do it in the next release.

## 3. The specific `apply_filters()` / `do_action()` calls listed

Every one of these is **not a hook this plugin defines** — they are calls
*into* another project's already-established, public hook name, for
interoperability:

- `admin/aab-importer.php:70,72` — `woocommerce_taxonomy_objects_*` /
  `woocommerce_taxonomy_args_*` are WooCommerce's own filter names. We call
  them so a site's existing WooCommerce customizations keep applying to
  taxonomies our importer registers; renaming them would silently break
  that interoperability (WooCommerce would never fire *our* renamed hook).
- `admin/base/WXRImporter.php:580,613` (and `:592`, `:598`, not separately
  listed but the same pattern) — `wp_import_post_data_processed` /
  `wp_import_post_terms` / `wp_import_insert_post` are the original
  WordPress Importer project's hook names (this file is an adapted fork,
  documented in `readme.txt` under "Third-party Libraries" as "WXR
  Importer 2.0 ... GPL-2.0-or-later —
  https://github.com/humanmade/WordPress-Importer"). Existing
  site code written against the standard WordPress importer's hooks
  continues to work unmodified against ours only because we kept the exact
  same names.

Each of these lines already carries an explicit
`// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound`
comment stating this reason in the source itself. This is the documented,
intentional exception the naming-convention rule itself makes for calling
another project's established hook — not an oversight.

## 4. `node_modules/flatted/php/flatted.php` (`Flatted`, `FlattedString`)

This file is part of an npm package's own PHP interop shim pulled in as a
transitive dependency under `node_modules/` — it is not code we authored,
and it never ships in the distributed plugin. `gulpfile.js` (the actual
release/zip build script, `npx gulp zip`) explicitly excludes
`node_modules/**` and `node_modules` from the generated package — see the
`sources` array at the top of that file. If this file is present in
whatever was scanned, that scan was run against the raw working directory
(post `npm install`) rather than the actual `dist/bricksfly-elements-for-bricks.zip`
this plugin ships — the two are not the same file set.

---

If any of the above still shows up on your next automated pass, please let
us know which specific item — we'll treat it as a signal that the scanner
isn't resolving namespaces/build exclusions the way we've described here,
and adjust accordingly (e.g. adding redundant class-name prefixes on top
of namespaces).
