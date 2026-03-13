=== WP Admin Dark Mode ===
Contributors: alexanderwagnerdev
Tags: dark mode, admin, dashboard, ui, accessibility
Requires at least: 6.0
Tested up to: 6.9.4
Requires PHP: 7.4
Stable tag: 0.0.3
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

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/wp-admin-dark-mode/` (or install via the Plugins screen).
2. Activate the plugin through the “Plugins” screen in WordPress.
3. Go to Settings → WP Admin Dark Mode and enable it.

== Frequently Asked Questions ==
= Does this affect the frontend? =
No. It only loads CSS in wp-admin.

= Where are the settings? =
Settings → WP Admin Dark Mode

= Can I customize the colors? =
Yes. The plugin includes multiple color pickers for the complete admin theme and also supports additional custom CSS.

== Changelog ==
= 0.0.3 =
* Full CSS overhaul with a token-based design system for backgrounds, surfaces, borders, text, links, buttons and semantic states
* Added new customizable color variables: `surface1`, `surface2`, `surface3`, `text_soft` and `danger`
* Increased the number of adjustable color tokens from 9 to 13
* Fixed the Plugin hinzufügen / page title action button styling so text stays readable in dark mode
* Fixed the WordPress Color Picker button styling by preserving the inline swatch background and styling only the text section
* Improved spacing, sizing and proportions to better match native WordPress admin UI defaults
* Extracted the settings page styles into a dedicated assets/css/settings.css file
* Reworked the settings page layout for cleaner spacing and more consistent component styling
* Improved plugin list styling, row actions, tables, notices, form controls, dashboard elements and navigation tabs
* Removed unwanted colored shadows / blue row highlight artifacts in plugin tables
* Updated plugin core file to version 0.0.3

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
Einfaches, schlankes Dark-Mode-Plugin für das WordPress Admin-Dashboard.

=== Beschreibung ===
Einfaches, schlankes Dark-Mode-Plugin für das WordPress Admin-Dashboard.

Funktionen:
* Ein-Klick aktivieren/deaktivieren
* Leichtgewichtiges CSS-basiertes Admin-Theme
* Funktioniert auf allen Admin-Seiten
* Individuelle Farbanpassung über den WordPress Color Picker
* Unterstützung für eigenes Custom CSS mit eingebauten CSS-Variablen
* Token-basiertes Design-System für Hintergründe, Texte, Rahmen, Buttons und Statusfarben

=== Installation ===
1. Lade den Plugin-Ordner nach `/wp-content/plugins/wp-admin-dark-mode/` hoch (oder installiere über „Plugins“).
2. Aktiviere das Plugin in WordPress unter „Plugins“.
3. Gehe zu Einstellungen → WP Admin Dark Mode und aktiviere es.

=== FAQ ===
= Betrifft das das Frontend? =
Nein. Es lädt nur CSS im wp-admin.

= Wo sind die Einstellungen? =
Einstellungen → WP Admin Dark Mode

= Kann ich die Farben anpassen? =
Ja. Das Plugin enthält mehrere Farbwähler für das komplette Admin-Theme und unterstützt zusätzlich eigenes Custom CSS.

=== Changelog ===
= 0.0.3 =
* Komplettes CSS-Refactoring mit token-basiertem Design-System für Hintergründe, Flächen, Rahmen, Texte, Links, Buttons und Statusfarben
* Neue anpassbare Farbvariablen hinzugefügt: `surface1`, `surface2`, `surface3`, `text_soft` und `danger`
* Anzahl der anpassbaren Farb-Tokens von 9 auf 13 erhöht
* Styling des "Plugin hinzufügen"-Buttons bzw. der Seitentitel-Actions korrigiert, damit der Text im Dark Mode lesbar bleibt
* WordPress Color Picker korrekt gefixt, indem die Inline-Hintergrundfarbe des Farbfelds erhalten bleibt und nur der Textbereich separat gestylt wird
* Abstände, Größen und Proportionen verbessert, damit das Verhältnis näher an der nativen WordPress-Admin-Oberfläche liegt
* Styles der Einstellungsseite in eine eigene Datei assets/css/settings.css ausgelagert
* Layout der Einstellungsseite für konsistentere Komponenten und sauberere Abstände überarbeitet
* Plugin-Liste, Row-Actions, Tabellen, Notices, Formularelemente, Dashboard-Bereiche und Tabs weiter optimiert
* Unerwünschte farbige Schatten bzw. blaue Hervorhebungs-Artefakte in Plugin-Tabellen entfernt
* Plugin-Kerndatei auf Version 0.0.3 aktualisiert

= 0.0.2 =
* Einstellungsseite komplett neu gestaltet: Card-Layout, Page-Header mit Status-Badge und Versionsanzeige
* Individuelle Farbanpassung für 9 Dark-Mode-Farben über den WordPress Color Picker
* Schaltfläche „Standardfarben wiederherstellen“ zum Zurücksetzen auf die WordPress-Sidebar-Palette
* Custom-CSS-Editor zum Einfügen eigener Styles nach dem Dark-Mode-Stylesheet
* Alle Dark-Mode-Farben basieren nun auf CSS-Custom-Properties (--adm-bg, --adm-card, etc.)
* Hintergrundfarbe auf #1d2327 geändert (native WordPress-Sidebar-Farbe)
* Dark-Mode-CSS vollständig überarbeitet: Admin Bar, Sidebar, Buttons, Formulare, Tabellen, Hinweise, Gutenberg, Medien, Bildschirmoptionen, Dashboard-Widgets
* Einstellungsseite passt sich selbst an den Dark Mode an, wenn dieser aktiv ist

= 0.0.1 =
* Erste Development-Version.
