# DarkAdmin – Dark Mode for Adminpanel

[![WordPress](https://img.shields.io/badge/WordPress-6.3%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPLv2%2B-green)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-0.1.0-orange)](https://github.com/AlexanderWagnerDev/wp-darkadmin-plugin)

<img src="DarkAdmin-Logo.png" alt="DarkAdmin-Logo" width="250px"/>

A simple, lightweight Dark Mode plugin for the WordPress Admin Dashboard with full color customization and Auto Dark Mode support.

---

## Features

- One-click enable/disable
- Lightweight CSS-based admin theme
- Works across all admin pages
- Individual color customization via WordPress Color Picker
- Custom CSS support using built-in CSS variables
- Token-based design system for backgrounds, text, borders, buttons and states
- Auto Dark Mode: automatically darkens bright plugin backgrounds not covered by the stylesheet
- Preset Themes: choose between Default (WP 6.x) and Modern (WP 7.0) color palettes
- Per-user Dark Mode access control (Include / Exclude) with empty-state UI when no non-admin users exist
- Excluded Pages: specify admin pages where dark mode should not be applied

---

## Installation

1. Upload the plugin folder to `/wp-content/plugins/darkadmin/`
2. Activate through **Plugins** in WordPress
3. Go to **Settings > DarkAdmin** and enable it

---

## Screenshots

| Settings – Dark Mode off | Settings – Dark Mode on |
|---|---|
| ![Screenshot 1](screenshot-1.png) | ![Screenshot 2](screenshot-2.png) |

| Dashboard – Dark Mode off | Dashboard – Dark Mode on |
|---|---|
| ![Screenshot 3](screenshot-3.png) | ![Screenshot 4](screenshot-4.png) |

---

## Changelog

### 0.1.0
- Added support for excluded pages in settings
- Added user access control (include/exclude users)
- Added preset themes (default and modern)
- Fixed critical JS bugs in preset and reset functionality
- Fixed missing closing brace in `initPaletteIO()` importFile block in `settings.js`
- Fixed XSS vulnerability in `printf` output (`settings-page.php`)
- Fixed Unicode escapes in language files: replaced `\uXXXX` sequences with literal UTF-8 characters
- Added `admI18n` JS localization via `wp_localize_script` for translated UI strings
- Removed redundant `wp-color-picker` script enqueue
- Added `.l10n.php` language cache files for all locales (`de_AT`, `de_DE`, `en_US`) with ABSPATH protection
- Added hex validation for JSON palette imports
- Updated documentation for new features

### 0.0.10
- Extended Themes section: added dark styling for `.theme-browser .theme .theme-name`, `.theme-overlay .theme-actions`, `.theme-overlay .theme-tags`, `.theme-overlay .theme-header .theme-title`, `.theme-overlay .theme-author`, `.theme-overlay .theme-version` and `.theme-overlay .theme-rating .star-rating .star`
- Added Theme Editor / Template Side section: dark styling for `#templateside > ul`, `.importer-title` and `.color-option.selected` / `.color-option:hover`
- Reduced `.cm-error` background opacity from `.15` to `.05` for a more subtle error highlight in CodeMirror
- All changes applied to both `darkadmin-dark.css` and `darkadmin-wp-modern.css`
- Fixed invalid control sequences in all language files (`de_AT`, `de_DE`, `en_US`, `.pot`): replaced `\uXXXX` Unicode escapes with literal UTF-8 characters to resolve msgfmt compilation errors
- User Access: Include and Exclude options are now greyed out and non-clickable when no non-administrator users exist
- User Access: replaced plain text fallback with a styled empty-state block
- i18n: added missing "No non-administrator users found" string to all language files

### 0.0.9
- Added Preset Themes: Default (WP 6.x classic dark) and Modern (WP 7.0 deep blue)
- Each preset ships with its own CSS file loaded dynamically based on the active preset
- Added `adm_preset` option with live preset switching
- Added per-user Dark Mode with User Access card
- Added live color preview (instant CSS variable updates without saving)
- Added Export / Import palette as JSON
- Added custom CSS sanitizer (`adm_sanitize_custom_css`)
- Added CSS cache-busting based on md5 hash of current color values
- Refactored plugin into modular includes
- Added `uninstall.php` for clean removal
- Color pickers grouped by category
- Expanded color tokens from 23 to 34

### 0.0.8
- Fixed invisible text in `.widefat` tables

### 0.0.7
- Added version to `darkadmin-dark.css` header comment

### 0.0.6
- Updated Text Domain to `darkadmin-dark-mode-for-adminpanel`
- Updated Plugin URI to wordpress.org
- Updated all language files to new text domain

### 0.0.5
- Rebranded from "WP Admin Dark Mode" to "DarkAdmin - Dark Mode for Adminpanel"
- Renamed main files, CSS files, text domain and menu slug
- Updated all language files

### 0.0.4
- Added Auto Dark Mode with WCAG luminance calculations
- Auto Dark Mode uses MutationObserver for AJAX-loaded content
- Fixed color picker swatch visibility
- Updated all translation files

### 0.0.3
- Full CSS overhaul with token-based design system
- Added new color variables
- Increased color tokens from 9 to 13
- Various styling fixes and layout improvements

### 0.0.2
- Redesigned settings page with card-based layout
- Added individual color customization via WordPress Color Picker
- Added Custom CSS editor
- All colors now driven by CSS custom properties

### 0.0.1
- Initial development release.

---

## License

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

---

*Developed by [AlexanderWagnerDev](https://alexanderwagnerdev.com)*
