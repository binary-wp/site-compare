# Changelog

## 0.1.1 — 2026-10-07

- **Changed:** works with `binary-wp/admin-guide` 0.14 as well as 0.13 (`^0.13 || ^0.14`). No code change; 0.14 kept the hooks this package uses (`booted`, `screens`, `viewer_actions`).

## 0.1.0 — 2026-10-01

- **Added:** Site Compare, extracted from `binary-wp/admin-guide` 0.12 (`src/Compare.php`). It now attaches with `SiteCompare::attach( $prefix, $args )` and plugs in only through the guide's public hooks: `booted`, `screens`, `viewer_actions`.
- **Added:** `nav` and `viewer_button` args.
- **Unchanged from 0.12:** page slug, settings option and AJAX action names, so existing galleries, saved URLs and capture tools keep working.
- **Changed:** text domain is now `binary-wp-site-compare`; license is GPL-2.0-or-later.
