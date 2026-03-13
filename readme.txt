=== WP Admin Dark Mode ===
Contributors: alexanderwagnerdev
Tags: dark mode, admin, dashboard, ui, accessibility
Requires at least: 6.0
Tested up to: 6.9.4
Requires PHP: 7.4
Stable tag: 0.0.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple, lightweight Dark Mode Plugin for the WordPress Admin Dashboard.

== Description ==
Simple, lightweight Dark Mode Plugin for the WordPress Admin Dashboard.

Features:
* One-click enable/disable
* Lightweight CSS-based admin theme
* Works across all admin pages
* Individual color customization via WordPress Color Picker
* Custom CSS support using built-in CSS variables
* Token-based design system for backgrounds, text, borders, buttons and states
* Auto Dark Mode: automatically darkens bright plugin backgrounds not covered by the stylesheet

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/wp-admin-dark-mode/` (or install via the Plugins screen).
2. Activate the plugin through the "Plugins" screen in WordPress.
3. Go to Settings > WP Admin Dark Mode and enable it.

== Frequently Asked Questions ==
= Does this affect the frontend? =
No. It only loads CSS in wp-admin.

= Where are the settings? =
Settings > WP Admin Dark Mode

= Can I customize the colors? =
Yes. The plugin includes multiple color pickers for the complete admin theme and also supports additional custom CSS.

= What is Auto Dark Mode? =
An optional second toggle that uses JavaScript to dynamically darken bright backgrounds and lighten dark text from third-party plugins not covered by the built-in stylesheet. Requires Dark Mode to be active.

== Changelog ==
= 0.0.4 =
* Added Auto Dark Mode option: optional JS-based pass that dynamically darkens bright backgrounds and lightens dark text from unknown plugins using WCAG luminance calculations
* Auto Dark Mode uses a MutationObserver to also handle AJAX-loaded content
* Fixed color picker swatch visibility: removed background-color from .wp-color-result in CSS so WP's inline style shows the swatch correctly
* Updated all translation files (de_AT, de_DE, en_US, .pot) with new Auto Dark Mode strings
* Cleaned up all comments and translated remaining German inline comments to English
* Bumped version to 0.0.4

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
