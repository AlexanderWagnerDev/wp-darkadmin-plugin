# Changelog

All notable changes to DarkAdmin - Dark Mode for Adminpanel are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [0.1.3] - 2026-03-26

### Changed
- Raised minimum WordPress version requirement to 6.7
- Raised minimum PHP version requirement to 8.0 (already required by existing use of `str_starts_with`, `str_contains` and named arguments)
- Added `defer` loading strategy to `darkadmin-settings-js` and `darkadmin-auto-darken` via the `strategy` argument introduced in WordPress 6.3

### Fixed
- Replaced inline `echo '<script>'` in `settings-page.php` with `wp_add_inline_script()`
- Replaced anonymous arrow function sanitize callbacks in `register_setting()` with named functions `darkadmin_sanitize_bool()`, `darkadmin_sanitize_user_ids()` and `darkadmin_sanitize_preset()`
- Used strict boolean check (`true === $value`) instead of loose cast in `darkadmin_sanitize_bool()`
- Removed direct `$_POST` access in `darkadmin_sanitize_colors()` and `darkadmin_sanitize_layout()`; preset value now read from `$input` array
- Added `shadow_md` value validation against a safe CSS pattern in `darkadmin_sanitize_layout()`
- Added late escaping via `wp_strip_all_tags()` to both `wp_add_inline_style()` calls for `$vars` and `$custom`
- Renamed generic JS object names `admData` and `admI18n` to `darkadminData` and `darkadminI18n` in `enqueue.php` and `settings.js`
- Added i18n string `"Copied!"` to `enqueue.php` via `wp_localize_script` (`darkadminI18n.copied`)
- Replaced hardcoded `'Copied!'` string in `settings.js` `initVarCopy()` with `darkadminI18n.copied` for full translateability
- Replaced `innerHTML` with `textContent` in `initVarCopy()` to prevent potential XSS
- Updated all language files (`.pot`, `de_AT`, `de_DE`, `en_US`): added `Copied!` / `Kopiert!` translation, bumped version to 0.1.3, updated timestamps
- Added missing `@package DarkAdmin` tag to `darkadmin.php` file comment
- Fixed `add_filter()` and `add_action()` calls in `darkadmin.php` to comply with WPCS multi-line function call rules: opening parenthesis is now the last content on the line, each argument on its own line, closing parenthesis on its own line
- Fixed equals sign alignment for `$has_users` in `settings-page.php` (7 spaces expected)
- Fixed closing PHP tag not on its own line in `settings-page.php` (`$prev` assignment block)
- Fixed opening PHP tag not on its own line in `settings-page.php` (`$current_color` block)
- Replaced short ternary `?:` with explicit `isset()` check and full ternary for `$current_color` in `settings-page.php`
- Fixed incorrect indentation in `settings-page.php` (10 tabs expected, 9 found)
- Fixed Yoda conditions for all comparisons in `settings-page.php`
- Replaced inline control structure with braced block in `settings-page.php`
- Replaced `$_GET['page']` with `get_current_screen()` in `enqueue.php` to avoid direct superglobal access
- Added `current_user_can()` capability check at the top of `darkadmin_settings_page()` in `settings-page.php`
- Added missing `darkadmin_layout` option cleanup in `uninstall.php`
- Fixed proper UTF-8 umlauts in `readme-de.txt` (replaced ASCII substitutions with correct characters)
- Replaced escaped HTML entity checkmark with literal UTF-8 character in preset button (`settings-page.php`)

---

## [0.1.2] - 2025

### Added
- Dedicated Sidebar color group with three new tokens: Sidebar Background (`--adm-sidebar-bg`), Sidebar Active Item (`--adm-sidebar-active`) and Sidebar Text (`--adm-sidebar-text`)
- Sidebar token translations to all language files (`de_AT`, `de_DE`, `en_US`, `.pot`, `.l10n.php`)
- Layout token system (spacing, radius, shadow) with per-preset defaults and settings UI
- `.adm-layout-grid` CSS: 4-column grid with responsive breakpoints and dark mode overrides

### Changed
- Unified layout tokens across presets, added layout JS handlers, updated all language files
- Improved color picker swatch display in settings page

### Fixed
- Fixed translators comment and `phpcs:ignore` placement in `settings-page.php`
- Replaced `&amp;` HTML entity with literal UTF-8 ampersand in i18n strings (`settings-page.php`)
- Replaced PHP `\u2713` escape with literal UTF-8 checkmark character in admin notice strings
- Replaced `&#10003;` HTML entity with literal UTF-8 checkmark in preset button PHP and all `.po` files
- Replaced ASCII-escaped umlauts with proper UTF-8 characters in all language files; added missing msgids (checkmark Active, em-dash in admin notice)
- Updated `darkadmin-dark.css` and `darkadmin-wp-modern.css`

---

## [0.1.1] - 2025

### Fixed
- `uninstall.php`: corrected all option names from wrong `adm_` prefix to `darkadmin_` prefix so options are properly removed on plugin deletion

---

## [0.1.0] - 2025

### Added
- Support for excluded pages in settings
- User access control (include/exclude users)
- Preset themes (Default and Modern)
- `admI18n` JS localization via `wp_localize_script` for translated UI strings
- Hex validation for JSON palette imports
- `.l10n.php` language cache files for all locales (`de_AT`, `de_DE`, `en_US`) with ABSPATH protection

### Fixed
- Critical JS bugs in preset and reset functionality
- Missing closing brace in `initPaletteIO()` importFile block in `settings.js`
- XSS vulnerability in `printf` output (`settings-page.php`)
- Unicode escapes in language files: replaced `\uXXXX` sequences with literal UTF-8 characters
- Removed redundant `wp-color-picker` script enqueue
- Updated documentation for new features

---

## [0.0.10] - 2025

### Added
- Extended Themes section: added dark styling for `.theme-browser .theme .theme-name`, `.theme-overlay .theme-actions`, `.theme-overlay .theme-tags`, `.theme-overlay .theme-header .theme-title`, `.theme-overlay .theme-author`, `.theme-overlay .theme-version` and `.theme-overlay .theme-rating .star-rating .star`
- Theme Editor / Template Side section: dark styling for `#templateside > ul`, `.importer-title` and `.color-option.selected` / `.color-option:hover`
- User Access: Include and Exclude options are now greyed out and non-clickable when no non-administrator users exist (disabled radio input + CSS `pointer-events: none`)
- User Access: replaced plain text fallback with a styled empty-state block (centered layout, dashed border, large icon)
- i18n: added missing string `"No non-administrator users found."` to `.pot`, `de_AT`, `de_DE` and `en_US` language files

### Changed
- Reduced `.cm-error` background opacity from `.15` to `.05` for a more subtle error highlight in CodeMirror
- All changes applied to both `darkadmin-dark.css` and `darkadmin-wp-modern.css`

### Fixed
- Fixed invalid control sequences in all language files (`de_AT`, `de_DE`, `en_US`, `.pot`): replaced `\uXXXX` Unicode escapes with literal UTF-8 characters to resolve `msgfmt` compilation errors

---

## [0.0.9] - 2025

### Added
- Preset Themes: choose between Default (WP 6.x classic dark) and Modern (WP 7.0 deep blue, glassmorphism-inspired) color palettes
- Each preset ships with its own CSS file (`darkadmin-dark.css` / `darkadmin-modern.css`) loaded dynamically based on the active preset
- `adm_preset` option with live preset switching on the settings page
- Per-user Dark Mode: administrators always have dark mode, non-admin users can be individually enabled via a new User Access card
- Live color preview: color picker changes update CSS variables instantly without saving
- Export / Import palette as JSON file
- Custom CSS sanitizer (`adm_sanitize_custom_css`) -- preserves valid CSS while stripping dangerous HTML/PHP tags
- CSS cache-busting based on `md5` hash of current color values
- Color pickers now grouped by category (Backgrounds, Surfaces, Borders, Text, Links, Brand, CodeMirror) on the settings page
- Expanded color tokens from 23 to 34 (new: `bg_bar`, `bg_deep`, `bg_darker`, `table_alt`, `plugin_inactive`, `border_hover`, `text_on_primary`, `link_hover`, `primary_hover`, `cm_keyword`-`cm_bracket`)

### Changed
- Refactored plugin into modular includes: `defaults.php`, `user-settings.php`, `enqueue.php`, `settings-page.php`
- Added `uninstall.php` to clean up all options on plugin removal

---

## [0.0.8] - 2025

### Fixed
- Fixed invisible text in `.widefat` tables (`update-core.php` and similar pages): override WP core rule `.widefat ol, .widefat p, .widefat ul { color: #2c3338 }` with dark theme color token

---

## [0.0.7] - 2025

### Changed
- Added version to `darkadmin-dark.css` header comment

---

## [0.0.6] - 2025

### Changed
- Updated Text Domain from `darkadmin` to `darkadmin-dark-mode-for-adminpanel` to match the wordpress.org plugin slug
- Updated Plugin URI to `https://wordpress.org/plugins/darkadmin-dark-mode-for-adminpanel/`
- Updated all language files (`.po`, `.pot`) to new text domain

---

## [0.0.5] - 2025

### Changed
- Rebranded plugin from "WP Admin Dark Mode" to "DarkAdmin - Dark Mode for Adminpanel"
- Renamed main plugin file from `wp-admin-dark-mode.php` to `darkadmin.php`
- Renamed CSS file from `wp-admin-dark.css` to `darkadmin-dark.css`
- Updated text domain from `wp-admin-dark-mode` to `darkadmin`
- Updated all language files (`de_AT`, `de_DE`, `en_US`, `.pot`) to new `darkadmin` text domain
- Updated settings menu slug from `wp-admin-dark-mode` to `darkadmin`
- Updated plugin folder reference in readme to `/wp-content/plugins/darkadmin/`

---

## [0.0.4] - 2025

### Added
- Auto Dark Mode option: optional JS-based pass that dynamically darkens bright backgrounds and lightens dark text from unknown plugins using WCAG luminance calculations
- Auto Dark Mode uses a `MutationObserver` to also handle AJAX-loaded content
- Updated all translation files (`de_AT`, `de_DE`, `en_US`, `.pot`) with new Auto Dark Mode strings

### Fixed
- Fixed color picker swatch visibility: removed `background-color` from `.wp-color-result` in CSS so WP's inline style shows the swatch correctly

---

## [0.0.3] - 2025

### Added
- Full CSS overhaul with a token-based design system for backgrounds, surfaces, borders, text, links, buttons and semantic states
- New customizable color variables: `surface1`, `surface2`, `surface3`, `text_soft` and `danger`
- Increased the number of adjustable color tokens from 9 to 13
- Extracted the settings page styles into a dedicated `assets/css/settings.css` file

### Fixed
- Fixed the Add Plugin / page title action button styling so text stays readable in dark mode
- Fixed the WordPress Color Picker button styling by preserving the inline swatch background and styling only the text section
- Improved spacing, sizing and proportions to better match native WordPress admin UI defaults
- Improved plugin list styling, row actions, tables, notices, form controls, dashboard elements and navigation tabs
- Removed unwanted colored shadows / blue row highlight artifacts in plugin tables

### Changed
- Reworked the settings page layout for cleaner spacing and more consistent component styling

---

## [0.0.2] - 2025

### Added
- Redesigned settings page with card-based layout, page header with status badge and version display
- Individual color customization for 9 dark mode colors via WordPress Color Picker
- "Restore Default Colors" button to reset all colors to the WordPress sidebar palette
- Custom CSS editor field to inject additional styles after the dark mode stylesheet
- All dark mode colors are now driven by CSS custom properties (`--adm-bg`, `--adm-card`, etc.)

### Changed
- Base background color changed to `#1d2327` (native WordPress sidebar color)
- Fully optimized dark mode CSS: Admin Bar, Sidebar, Buttons, Forms, Tables, Notices, Gutenberg, Media, Screen Options, Dashboard Widgets
- Settings page itself adapts to dark mode when active

---

## [0.0.1] - 2025

### Added
- Initial development release
