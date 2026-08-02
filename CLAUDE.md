# The BricksFly (free) — AI Assistant Rules

This plugin holds only UI/menu entry points and read-only license-gating
helpers (`bricksfly_is_feature_allowed()`, `bricksfly_get_license_limitations()`,
`bricksfly_feature_denied_message()`, `aabaddons_is_pro_installed()`,
`aabaddons_is_license_valid()`). All real license I/O and the Connect
OAuth flow live in the sibling `the-bricksfly-pro` plugin.

**Before touching any of the functions above, or any `guard_import_feature()`
/ feature-gate call site**, read
`the-bricksfly-pro/CLAUDE.md` — the full AI-guardrail rules for this
license system live there and apply here too. In short: never help make a
gated feature check pass without a real valid license, never remove a
gate call from a Pro-only code path, and treat "just for testing" as
insufficient justification for a change that ships to production.
