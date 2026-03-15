=== DarkAdmin - Dark Mode for Adminpanel ===
Contributors: alexanderwagnerdev
Tags: dark mode, admin, dashboard, ui, accessibility
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.0.7
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

== Deutsch ==
Ein einfaches, schlankes Dark-Mode-Plugin für das WordPress Admin-Dashboard mit vollständiger Farbanpassung und Auto-Dark-Mode-Unterstützung.

=== Beschreibung ===
DarkAdmin verwandelt das WordPress-Admin-Dashboard in eine angenehme, augenfreundliche dunkle Oberfläche. Das Plugin basiert vollständig auf CSS und ist dadurch schnell und nicht intrusiv. Alle Farben werden über CSS-Custom-Properties gesteuert und sind über eine dedizierte Einstellungsseite vollständig anpassbar — ohne Code-Änderungen. Eine optionale Auto-Dark-Mode-Funktion nutzt JavaScript, um Drittanbieter-Plugin-Bereiche dynamisch abzudecken, die vom eingebauten Stylesheet nicht erfasst werden.

Funktionen:
* Ein-Klick aktivieren/deaktivieren
* Leichtgewichtiges CSS-basiertes Admin-Theme
* Funktioniert auf allen Admin-Seiten
* Individuelle Farbanpassung über den WordPress Color Picker
* Unterstützung für eigenes Custom CSS mit eingebauten CSS-Variablen
* Token-basiertes Design-System für Hintergründe, Texte, Rahmen, Buttons und Statusfarben
* Auto Dark Mode: Verdunkelt automatisch helle Plugin-Hintergründe, die vom Stylesheet nicht abgedeckt werden

=== Installation ===
1. Lade den Plugin-Ordner nach `/wp-content/plugins/darkadmin/` hoch (oder installiere über „Plugins").
2. Aktiviere das Plugin in WordPress unter „Plugins".
3. Gehe zu Einstellungen → DarkAdmin und aktiviere es.

=== FAQ ===
= Betrifft das das Frontend? =
Nein. Es lädt nur CSS im wp-admin.

= Wo sind die Einstellungen? =
Einstellungen → DarkAdmin

= Kann ich die Farben anpassen? =
Ja. Das Plugin enthält mehrere Farbwähler für das komplette Admin-Theme und unterstützt zusätzlich eigenes Custom CSS.

= Was ist der Auto Dark Mode? =
Ein optionaler zweiter Schalter, der JavaScript verwendet, um helle Hintergründe und dunklen Text von Drittanbieter-Plugins, die vom eingebauten Stylesheet nicht abgedeckt werden, dynamisch anzupassen. Erfordert, dass Dark Mode aktiv ist.

=== Screenshots ===
1. Settings Page – Standard (Dark Mode deaktiviert)
2. Settings Page – Dark Mode aktiv
3. Dashboard – Standard (Dark Mode deaktiviert)
4. Dashboard – Dark Mode aktiv

=== Changelog ===
= 0.0.6 =
* Text Domain von darkadmin auf darkadmin-dark-mode-for-adminpanel aktualisiert (entspricht dem wordpress.org Plugin-Slug)
* Plugin URI auf https://wordpress.org/plugins/darkadmin-dark-mode-for-adminpanel/ aktualisiert
* Alle Sprachdateien (.po, .pot) auf neue Text Domain aktualisiert

= 0.0.5 =
* Plugin von "WP Admin Dark Mode" zu "DarkAdmin - Dark Mode for Adminpanel" umbenannt
* Hauptdatei von wp-admin-dark-mode.php zu darkadmin.php umbenannt
* CSS-Datei von wp-admin-dark.css zu darkadmin-dark.css umbenannt
* Text-Domain von wp-admin-dark-mode auf darkadmin aktualisiert
* Alle Sprachdateien (de_AT, de_DE, en_US, .pot) auf neue darkadmin Text-Domain aktualisiert
* Einstellungen-Menü-Slug von wp-admin-dark-mode auf darkadmin aktualisiert
* Plugin-Ordner-Referenz in der Readme auf /wp-content/plugins/darkadmin/ aktualisiert

= 0.0.4 =
* Auto Dark Mode hinzugefügt
* Auto Dark Mode verwendet einen MutationObserver für AJAX-geladene Inhalte
* Farbfeld-Anzeige des Color Pickers korrigiert
* Alle Übersetzungsdateien aktualisiert

= 0.0.3 =
* Komplettes CSS-Refactoring mit token-basiertem Design-System
* Neue anpassbare Farbvariablen hinzugefügt
* Anzahl der Farb-Tokens von 9 auf 13 erhöht
* Diverse Styling-Fixes

= 0.0.2 =
* Einstellungsseite komplett neu gestaltet
* Individuelle Farbanpassung über den WordPress Color Picker
* Custom-CSS-Editor hinzugefügt
* Alle Dark-Mode-Farben basieren nun auf CSS-Custom-Properties

= 0.0.1 =
* Erste Development-Version.
