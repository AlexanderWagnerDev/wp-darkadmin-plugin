# WP Admin Dark Mode

Simple, lightweight Dark Mode toggle for the WordPress Admin Dashboard.

## Plugin Info

- **Contributors:** alexanderwagnerdev
- **Requires at least:** 6.0
- **Tested up to:** 6.9.4
- **Requires PHP:** 7.4
- **Stable tag:** 0.0.2
- **License:** GPLv2 or later (https://www.gnu.org/licenses/gpl-2.0.html)

## Description

Admin Dark Mode adds a minimal on/off toggle for dark styling inside `wp-admin`.

### Features

- One-click enable/disable
- Lightweight CSS only
- Works across all admin pages

## Installation

1. Upload the plugin folder to `/wp-content/plugins/wp-admin-dark-mode/` (or install via the Plugins screen).
2. Activate the plugin through the “Plugins” screen in WordPress.
3. Go to **Settings → Dark Mode** and enable it.

## FAQ

### Does this affect the frontend?

No. It only loads CSS in `wp-admin`.

### Where is the toggle?

**Settings → Dark Mode**.

## Changelog

### 0.0.2

- Redesigned settings page with card-based layout, page header with status badge and version display
- Added individual color customization for 9 dark mode colors via WordPress Color Picker
- Added “Restore Default Colors” button to reset all colors to the WordPress sidebar palette
- Added Custom CSS editor field to inject additional styles after the dark mode stylesheet
- All dark mode colors are now driven by CSS custom properties (`--adm-bg`, `--adm-card`, etc.)
- Base background color changed to `#1d2327` (native WordPress sidebar color)
- Fully optimized dark mode CSS: Admin Bar, Sidebar, Buttons, Forms, Tables, Notices, Gutenberg, Media, Screen Options, Dashboard Widgets
- Settings page itself adapts to dark mode when active

### 0.0.1

- Initial development release.

---

# Deutsch

Einfacher, schlanker Dark-Mode-Schalter für das WordPress Admin-Dashboard.

## Beschreibung

Admin Dark Mode fügt einen minimalistischen Ein/Aus-Schalter für ein dunkles Design in `wp-admin` hinzu.

### Funktionen

- Ein-Klick aktivieren/deaktivieren
- Nur leichtgewichtiges CSS
- Funktioniert auf allen Admin-Seiten

## Installation

1. Lade den Plugin-Ordner nach `/wp-content/plugins/admin-dark-mode/` hoch (oder installiere über „Plugins“).
2. Aktiviere das Plugin in WordPress unter „Plugins“.
3. Gehe zu **Einstellungen → Dark Mode** und aktiviere es.

## FAQ

### Betrifft das das Frontend?

Nein. Es lädt nur CSS im `wp-admin`.

### Wo ist der Schalter?

**Einstellungen → Dark Mode**.

## Changelog

### 0.0.2

- Einstellungsseite komplett neu gestaltet: Card-Layout, Page-Header mit Status-Badge und Versionsanzeige
- Individuelle Farbanpassung für 9 Dark-Mode-Farben über den WordPress Color Picker
- Schaltfläche „Standardfarben wiederherstellen“ zum Zurücksetzen auf die WordPress-Sidebar-Palette
- Custom-CSS-Editor zum Einfügen eigener Styles nach dem Dark-Mode-Stylesheet
- Alle Dark-Mode-Farben basieren nun auf CSS-Custom-Properties (`--adm-bg`, `--adm-card`, etc.)
- Hintergrundfarbe auf `#1d2327` geändert (native WordPress-Sidebar-Farbe)
- Dark-Mode-CSS vollständig überarbeitet: Admin Bar, Sidebar, Buttons, Formulare, Tabellen, Hinweise, Gutenberg, Medien, Bildschirmoptionen, Dashboard-Widgets
- Einstellungsseite passt sich selbst an den Dark Mode an, wenn dieser aktiv ist

### 0.0.1

- Erste Development-Version.
