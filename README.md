# binary-wp/site-compare

Site Compare for [binary-wp/admin-guide](https://github.com/binary-wp/admin-guide).

An opt-in tool page that renders a **source-vs-target visual comparison gallery**: every URL scraped from the source site's sitemap is captured full-height on both origins by a host-provided node capture tool and shown side by side. This package contributes the admin surface only — settings form, capture toolbar (regenerate / stop / status), same-origin gallery iframe, and a collapsible URL inventory table read from the gallery's `manifest.json`.

Attach it to a booted (or about-to-boot) [binary-wp/admin-guide](https://github.com/binary-wp/admin-guide) instance:

```php
\BinaryWP\SiteCompare\SiteCompare::attach( 'my_prefix', [
    // URL defaults — editable on the page itself; saved values (stored in
    // the {prefix}_admin_guide_compare option) win over these:
    'source_base'    => 'https://www.example.com',                    // production origin
    'source_sitemap' => 'https://www.example.com/sitemap_index.xml',  // URL scraper input
    'target_base'    => 'https://example.local',                      // default: home_url()

    // Capture tool wiring (host-provided, node-based):
    'wrapper'    => '/path/to/bin/visual-compare.sh',  // capture shell wrapper
    'tool_dir'   => '/path/to/bin/visual-compare',     // default: wrapper minus '.sh'
    'node_bin'   => '/usr/local/bin/node',             // default shown; falls back to `command -v node`
    'output_rel' => 'migrate/compare',                 // gallery dir under uploads (default shown)

    // Page:
    'menu_label'    => 'Site Compare',   // default shown
    'capability'    => 'manage_options', // default: the guide instance capability
    'nav'           => 'menu',           // 'menu' | 'builder' | 'hidden'
    'viewer_button' => true,             // button in the guide Viewer toolbar
] );
```

The page registers through the guide's `screens` hook as `?page={prefix}-admin-guide-compare`, under the guide's `menu.parent`. A `viewer_actions` button links to it from the guide Viewer.

How the pieces fit:

- **Settings** (three URL fields) are stored in the `{prefix}_admin_guide_compare` option; non-empty saved values override the boot defaults. Regenerate passes them to the wrapper as `--sitemap` / `--prod-base` / `--local-base` (plus `--out` for the uploads gallery dir), so the config actually drives capture.
- **Regenerate** runs detached (`nohup`) with a PID lockfile in the gallery dir; the status poll checks the PID is a *live* process, so a crashed run recovers instantly instead of showing a phantom "capturing…". Modes: full sitemap, demo (`--demo`), per-sitemap-type (`--types <slug>`), plus a "first N" `--limit` and an incremental flag.
- **The gallery** (`index.html` + `manifest.json` + screenshots) lives under `wp-content/uploads/{output_rel}/`, i.e. it's served by WordPress on the *same origin* as wp-admin. The iframe loads it with `?wp=1&ajax=<admin-ajax>&nonce=…&action={prefix}_admin_guide_compare_refresh` so the gallery's own per-page refresh buttons can call `admin-ajax.php` directly — no CORS, no control server.
- **Graceful degradation**: if `shell_exec` is disabled or the wrapper / tool dir / node binary is missing, the settings form, read-only gallery, and URL inventory still work — only the capture toolbar is disabled, with a notice naming what's missing.

Requirements for the interactive toolbar: `shell_exec` enabled, node, and a capture tool matching the wrapper contract (a `capture.mjs`-style tool with a `refresh-cli.mjs` sibling — see the Wolfe's Neck `bin/visual-compare/` reference implementation). This is a dev/migration aid; don't ship it enabled on production.

## Install

```bash
composer require binary-wp/site-compare
```

Requires `binary-wp/admin-guide` ^0.13 or ^0.14. The extension uses only the guide's public hooks
(`booted`, `screens`, `viewer_actions`), so it doubles as a worked example of an
admin-guide extension.

## Migrating from admin-guide ≤ 0.12

Up to 0.12 this shipped inside admin-guide and was switched on with a `compare` boot arg.
Admin-guide 0.13 ignores that arg and logs a `_doing_it_wrong` notice. To migrate, move the
same array into `attach()`:

```php
// before
Plugin::boot( 'wn', [ /* … */ 'compare' => $compare_args ] );

// after
Plugin::boot( 'wn', [ /* … */ ] );
\BinaryWP\SiteCompare\SiteCompare::attach( 'wn', $compare_args );
```

Nothing else changes: the page slug, the `{prefix}_admin_guide_compare` option holding the
saved URLs, and the `{prefix}_admin_guide_compare_*` AJAX actions keep their names.
