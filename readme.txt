=== DarkAdmin - Dark Mode for Adminpanel ===
Contributors: alexanderwagnerdev
Tags: dark mode, admin, dashboard, ui, accessibility
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.0.10
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple, lightweight Dark Mode plugin for the WordPress Admin Dashboard with full color customization and Auto Dark Mode support.

== Description ==
DarkAdmin transforms the WordPress admin dashboard into a clean, eye-friendly dark interface. The plugin is purely CSS-based, keeping it fast and non-intrusive. All colors are driven by CSS custom properties, making the theme fully customizable through a dedicated settings page — no code editing required. An optional Auto Dark Mode feature uses JavaScript to dynamically handle third-party plugin areas not covered by the built-in stylesheet.

Features:
* One-click enable/disable
* Lightweight CSS-based admin theme
* Works across all admin pages
* Individual color customization via WordPress Color Picker
* Custom CSS support using built-in CSS variables
* Token-based design system for backgrounds, text, borders, buttons and states
* Auto Dark Mode: automatically darkens bright plugin backgrounds not covered by the stylesheet
* Preset Themes: choose between Default (WP 6.x) and Modern (WP 7.0) color palettes
* Per-user Dark Mode access control (Include / Exclude) with empty-state UI when no non-admin users exist

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/darkadmin/` (or install via the Plugins screen).
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Go to Settings > DarkAdmin and enable it.

== Frequently Asked Questions ==
= Does this affect the frontend? =
No. It only loads CSS in wp-admin.

= Where are the settings? =
Settings > DarkAdmin

= Can I customize the colors? =
Yes. The plugin includes multiple color pickers for the complete admin theme and also supports additional custom CSS.

= What is Auto Dark Mode? =
An optional second toggle that uses JavaScript to dynamically darken bright backgrounds and lighten dark text from third-party plugins not covered by the built-in stylesheet. Requires Dark Mode to be active.

== Screenshots ==
1. Settings Page – Default (Dark Mode off)
2. Settings Page – Dark Mode active
3. Dashboard – Default (Dark Mode off)
4. Dashboard – Dark Mode active

== Changelog ==
= 0.0.10 =
* Extended Themes section: added dark styling for .theme-browser .theme .theme-name, .theme-overlay .theme-actions, .theme-overlay .theme-tags, .theme-overlay .theme-header .theme-title, .theme-overlay .theme-author, .theme-overlay .theme-version and .theme-overlay .theme-rating .star-rating .star
* Added Theme Editor / Template Side section: dark styling for #templateside > ul, .importer-title and .color-option.selected / .color-option:hover
* Reduced .cm-error background opacity from .15 to .05 for a more subtle error highlight in CodeMirror
* All changes applied to both darkadmin-dark.css and darkadmin-wp-modern.css
* Fixed invalid control sequences in all language files (de_AT, de_DE, en_US, .pot): replaced \uXXXX Unicode escapes with literal UTF-8 characters to resolve msgfmt compilation errors
* User Access: Include and Exclude options are now greyed out and non-clickable when no non-administrator users exist (disabled radio input + CSS pointer-events: none)
* User Access: replaced plain text fallback with a styled empty-state block (centered layout, dashed border, large icon)
* i18n: added missing string "No non-administrator users found. Create additional users to manage their dark mode access here." to .pot, de_AT, de_DE and en_US language files

= 0.0.9 =
* Added Preset Themes: choose between Default (WP 6.x classic dark) and Modern (WP 7.0 deep blue, glassmorphism-inspired) color palettes
* Each preset ships with its own CSS file (darkadmin-dark.css / darkadmin-modern.css) loaded dynamically based on the active preset
* Added adm_preset option with live preset switching on the settings page
* Added per-user Dark Mode: administrators always have dark mode, non-admin users can be individually enabled via a new User Access card
* Added live color preview: color picker changes update CSS variables instantly without saving
* Added Export / Import palette as JSON file
* Added custom CSS sanitizer (adm_sanitize_custom_css) — preserves valid CSS while stripping dangerous HTML/PHP tags
* Added CSS cache-busting based on md5 hash of current color values
* Refactored plugin into modular includes: defaults.php, user-settings.php, enqueue.php, settings-page.php
* Added uninstall.php to clean up all options on plugin removal
* Color pickers now grouped by category (Backgrounds, Surfaces, Borders, Text, Links, Brand, CodeMirror) on the settings page
* Expanded color tokens from 23 to 34 (new: bg_bar, bg_deep, bg_darker, table_alt, plugin_inactive, border_hover, text_on_primary, link_hover, primary_hover, cm_keyword–cm_bracket)

= 0.0.8 =
* Fixed invisible text in .widefat tables (update-core.php and similar pages): override WP core rule `.widefat ol, .widefat p, .widefat ul { color: #2c3338 }` with dark theme color token

= 0.0.7 =
* Added version to darkadmin-dark.css header comment

= 0.0.6 =
* Updated Text Domain from darkadmin to darkadmin-dark-mode-for-adminpanel to match the wordpress.org plugin slug
* Updated Plugin URI to https://wordpress.org/plugins/darkadmin-dark-mode-for-adminpanel/
* Updated all language files (.po, .pot) to new text domain

= 0.0.5 =
* Rebranded plugin from "WP Admin Dark Mode" to "DarkAdmin - Dark Mode for Adminpanel"
* Renamed main plugin file from wp-admin-dark-mode.php to darkadmin.php
* Renamed CSS file from wp-admin-dark.css to darkadmin-dark.css
* Updated text domain from wp-admin-dark-mode to darkadmin
* Updated all language files (de_AT, de_DE, en_US, .pot) to new darkadmin text domain
* Updated settings menu slug from wp-admin-dark-mode to darkadmin
* Updated plugin folder reference in readme to /wp-content/plugins/darkadmin/

= 0.0.4 =
* Added Auto Dark Mode option: optional JS-based pass that dynamically darkens bright backgrounds and lightens dark text from unknown plugins using WCAG luminance calculations
* Auto Dark Mode uses a MutationObserver to also handle AJAX-loaded content
* Fixed color picker swatch visibility: removed background-color from .wp-color-result in CSS so WP's inline style shows the swatch correctly
* Updated all translation files (de_AT, de_DE, en_US, .pot) with new Auto Dark Mode strings

= 0.0.3 =
* Full CSS overhaul with a token-based design system for backgrounds, surfaces, borders, text, links, buttons and semantic states
* Added new customizable color variables: `surface1`, `surface2`, `surface3`, `text_soft` and `danger`
* Increased the number of adjustable color tokens from 9 to 13
* Fixed the Add Plugin / page title action button styling so text stays readable in dark mode
* Fixed the WordPress Color Picker button styling by preserving the inline swatch background and styling only the text section
* Improved spacing, sizing and proportions to better match native WordPress admin UI defaults
* Extracted the settings page styles into a dedicated assets/css/settings.css file
* Reworked the settings page layout for cleaner spacing and more consistent component styling
* Improved plugin list styling, row actions, tables, notices, form controls, dashboard elements and navigation tabs
* Removed unwanted colored shadows / blue row highlight artifacts in plugin tables

= 0.0.2 =
* Redesigned settings page with card-based layout, page header with status badge and version display
* Added individual color customization for 9 dark mode colors via WordPress Color Picker
* Added "Restore Default Colors" button to reset all colors to the WordPress sidebar palette
* Added Custom CSS editor field to inject additional styles after the dark mode stylesheet
* All dark mode colors are now driven by CSS custom properties (--adm-bg, --adm-card, etc.)
* Base background color changed to #1d2327 (native WordPress sidebar color)
* Fully optimized dark mode CSS: Admin Bar, Sidebar, Buttons, Forms, Tables, Notices, Gutenberg, Media, Screen Options, Dashboard Widgets
* Settings page itself adapts to dark mode when active

= 0.0.1 =
* Initial development release.
