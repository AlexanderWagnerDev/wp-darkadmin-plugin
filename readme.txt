=== DarkAdmin - Dark Mode for Adminpanel ===
Contributors: alexanderwagnerdev
Tags: dark mode, admin, dashboard, ui, accessibility
Requires at least: 6.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: dev2026032802
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
* Excluded Pages: specify admin pages where dark mode should not be applied

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/darkadmin-dark-mode-for-adminpanel/` (or install via the Plugins screen).
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
1. Settings Page - Default (Dark Mode off)
2. Settings Page - Dark Mode active
3. Dashboard - Default (Dark Mode off)
4. Dashboard - Dark Mode active

== Changelog ==
