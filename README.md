# Check System Details

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/check-system-details)](https://wordpress.org/plugins/check-system-details/)
[![WordPress Tested Up To](https://img.shields.io/wordpress/plugin/tested/check-system-details)](https://wordpress.org/plugins/check-system-details/)
[![WordPress Plugin Downloads](https://img.shields.io/wordpress/plugin/dt/check-system-details)](https://wordpress.org/plugins/check-system-details/)
[![License: GPL v2](https://img.shields.io/badge/license-GPLv2-blue.svg)](license.txt)

A WordPress admin plugin that reports your site's WordPress, server, and database details in one place — and now actively flags real problems instead of just dumping raw facts.

**WordPress.org plugin page:** https://wordpress.org/plugins/check-system-details/

## Features

**Health Check**
- Flags real problems, not just facts: outdated/EOL PHP versions, missing PHP extensions (curl, gd/imagick, mbstring, zip, openssl, dom), OPcache status, `WP_DEBUG` display risk, disabled file editor, HTTPS status, low memory limits, `wp-config.php` file permissions, writable `wp-content`
- Detects page caching plugins (WP Rocket, W3 Total Cache, WP Super Cache, LiteSpeed Cache, Cache Enabler, WP Fastest Cache, SiteGround Optimizer) and persistent object caches (Redis/Memcached)
- Lists real scheduled cron events with next-run time and recurrence, and flags overdue/stuck cron
- Multisite network site list (ID, domain, path) instead of just a true/false flag

**WordPress, Server & Database details**
- WordPress URLs, version, multisite, memory limit, debug mode, cron, language
- Active theme and installed plugin details
- Server details (PHP limits) and database details (name, host, charset, prefix, table list)

**Files & Logs**
- `.htaccess` content
- `robots.txt` content
- Last 100 lines of `debug.log`

**Built for support handoff**
- Tabbed UI instead of one long scrolling page
- One-click **Copy Report** and **Download Report (.txt)** — grabs everything across all tabs as plain text, ready to paste into a support ticket

**Utilities**
- **Test Email** — one-click check that `wp_mail()` actually delivers
- **Snapshot & Compare** — save the current report, then diff a later one against it to see exactly what changed after something broke
- **Email Report** — send the report to any address on demand, or schedule it weekly/monthly to a chosen recipient

**WP-CLI**
- `wp check-system-details` prints the full report from the terminal - useful for scripts, SSH-only access, or agencies managing many sites

**Privacy & polish**
- **Report Privacy** toggle — redacts database name/host and server file paths from Copy/Download/Email Report (on by default)
- Pending update indicators next to WordPress core, the active theme, and installed plugins
- Individual copy buttons for `.htaccess`, `robots.txt`, and `debug.log`
- Tooltips on WordPress, Server & Database, Files & Logs, and Utilities fields
- A **Docs** tab explaining every tab and how to use the Utilities
- "Settings" quick link on the Plugins list page
- Clean uninstall (removes stored options and scheduled cron)
- Translation-ready (`check-system-details` text domain)

## Installation

**From WordPress.org (recommended)**
1. In your WordPress admin, go to **Plugins → Add New**
2. Search for "Check System Details"
3. Install and activate

Or install directly from the [plugin page](https://wordpress.org/plugins/check-system-details/).

**From this repository**
1. Download or clone this repo
2. Copy the folder into `wp-content/plugins/`
3. Activate it from **Plugins** in your WordPress admin

## Usage

After activating, go to **System Details** in the WordPress admin sidebar. The Health Check tab loads first and gives a quick Good/Warning/Critical breakdown; other tabs hold the raw WordPress, server, database, and file details.

## Screenshots

See `assets/screenshot-1.png` through `screenshot-6.png` (Health Check, WordPress, Server & Database, Files & Logs, Utilities, and Docs tabs), and the [WordPress.org listing](https://wordpress.org/plugins/check-system-details/) once published there.

## Changelog

Full changelog is in [readme.txt](readme.txt) (WordPress.org format). Highlights:

- **1.2.0** — Tabbed UI revamp, Health Check tab, Copy/Download Report, Utilities tab (test email, snapshot/compare, email report), WP-CLI command, Report Privacy/redaction, pending update indicators, tooltips, Docs tab, Settings quick link, clean uninstall, i18n, capability bug fix, output escaping hardening
- **1.1.0** — Added `.htaccess`, `robots.txt`, and `debug.log` viewing
- **1.0.0** — Initial release

## Contributing

Issues and pull requests are welcome on this repository.

## License

GPL-2.0+. See [license.txt](license.txt).

## Author

[Prabhat Rai](https://prabhatrai.in/)
