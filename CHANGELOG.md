# Changelog

## 0.1.0 — unreleased

- **Added:** Site Compare, extracted from `binary-wp/admin-guide` 0.12 (`src/Compare.php`). It now attaches with `SiteCompare::attach( $prefix, $args )` and plugs in only through the guide's public hooks: `booted`, `screens`, `viewer_actions`.
- **Added:** `nav` and `viewer_button` args.
- **Unchanged from 0.12:** page slug, settings option and AJAX action names, so existing galleries, saved URLs and capture tools keep working.
- **Changed:** text domain is now `binary-wp-site-compare`; license is GPL-2.0-or-later.
