# DarkAdmin - Dark Mode for Adminpanel

<p align="center">
  <img src="https://raw.githubusercontent.com/AlexanderWagnerDev/wp-darkadmin-plugin/main/DarkAdmin-Logo.png" alt="DarkAdmin Logo" width="128" />
</p>

<p align="center">
  A simple, lightweight Dark Mode plugin for the WordPress Admin Dashboard. It replaces the default bright admin interface with a carefully crafted dark theme, supports full color customization via CSS variables, and includes an Auto Dark Mode feature for third-party plugin compatibility.
</p>

---

## Plugin Info

| | |
|---|---|
| **Contributors** | AlexanderWagnerDev |
| **Plugin URI** | [wordpress.org/plugins/darkadmin-dark-mode-for-adminpanel](https://wordpress.org/plugins/darkadmin-dark-mode-for-adminpanel/) |
| **Requires at least** | 6.0 |
| **Tested up to** | 6.9 |
| **Requires PHP** | 7.4 |
| **Stable tag** | 0.0.7 |
| **License** | GPLv2 or later |

## Description

DarkAdmin transforms the WordPress admin dashboard into a clean, eye-friendly dark interface. The plugin is purely CSS-based, keeping it fast and non-intrusive. All colors are driven by CSS custom properties, making the theme fully customizable through a dedicated settings page — no code editing required. An optional Auto Dark Mode feature uses JavaScript to dynamically handle third-party plugin areas not covered by the built-in stylesheet.

### Features

- One-click enable/disable
- Lightweight CSS-based admin theme
- Works across all admin pages
- Individual color customization via WordPress Color Picker
- Custom CSS support using built-in CSS variables
- Token-based design system for backgrounds, text, borders, buttons and states
- Auto Dark Mode: dynamically darkens bright plugin backgrounds not covered by the stylesheet

## Screenshots

### Settings Page – Default (Dark Mode off)
![DarkAdmin – Settings Page without Dark Mode](screenshot-1.png)

### Settings Page – Dark Mode active
![DarkAdmin – Settings Page with Dark Mode enabled](screenshot-2.png)

### Dashboard – Default (Dark Mode off)
![DarkAdmin – Dashboard without Dark Mode](screenshot-3.png)

### Dashboard – Dark Mode active
![DarkAdmin – Dashboard with Dark Mode enabled](screenshot-4.png)

## Installation

1. Upload the plugin folder to `/wp-content/plugins/darkadmin/` (or install via the Plugins screen).
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to **Settings → DarkAdmin** and enable it.

## FAQ

### Does this affect the frontend?

No. It only loads CSS in `wp-admin`.

### Where are the settings?

**Settings → DarkAdmin**

### Can I customize the colors?

Yes. The plugin includes multiple color pickers for the complete admin theme and also supports additional custom CSS.

### What is Auto Dark Mode?

An optional second toggle that uses JavaScript to dynamically darken bright backgrounds and lighten dark text from third-party plugins not covered by the built-in stylesheet. Requires Dark Mode to be active.

## Changelog

### 0.0.7

- Fixed Plugin Upload Form (Plugins > Add New > Upload Plugin) not being styled correctly due to WP core CSS specificity conflict
- Replaced generic `.upload-plugin .wp-upload-form` selectors with scoped `body.wp-admin div.upload-plugin > form.wp-upload-form` to override WordPress default `background: #f6f7f7` and `border: #c3c4c7`
- Fixed all language files (`.po`, `.pot`, `de_AT`, `de_DE`, `en_US`): replaced invalid `\uXXXX` Unicode escape sequences with native UTF-8 characters to resolve `msgfmt` fatal errors

### 0.0.6

- Updated Text Domain from `darkadmin` to `darkadmin-dark-mode-for-adminpanel` to match the wordpress.org plugin slug
- Updated `Plugin URI` to `https://wordpress.org/plugins/darkadmin-dark-mode-for-adminpanel/`
- Updated all language files (`.po`, `.pot`) to new text domain

### 0.0.5

- Rebranded plugin from "WP Admin Dark Mode" to "DarkAdmin - Dark Mode for Adminpanel"
- Renamed main plugin file from `wp-admin-dark-mode.php` to `darkadmin.php`
- Renamed CSS file from `wp-admin-dark.css` to `darkadmin-dark.css`
- Updated text domain from `wp-admin-dark-mode` to `darkadmin`
- Updated all language files (`de_AT`, `de_DE`, `en_US`, `.pot`) to new `darkadmin` text domain
- Updated settings menu slug from `wp-admin-dark-mode` to `darkadmin`
- Updated plugin folder reference in readme to `/wp-content/plugins/darkadmin/`

### 0.0.4

- Added Auto Dark Mode option: optional JS-based pass that dynamically darkens bright backgrounds and lightens dark text from unknown plugins using WCAG luminance calculations
- Auto Dark Mode uses a MutationObserver to also handle AJAX-loaded content
- Fixed color picker swatch visibility: removed `background-color` from `.wp-color-result` in CSS so WP's inline style shows the swatch correctly
- Updated all translation files (`de_AT`, `de_DE`, `en_US`, `.pot`) with new Auto Dark Mode strings

### 0.0.3

- Full CSS overhaul with a token-based design system for backgrounds, surfaces, borders, text, links, buttons and semantic states
- Added new customizable color variables: `surface1`, `surface2`, `surface3`, `text_soft` and `danger`
- Increased the number of adjustable color tokens from 9 to 13
- Fixed the **Add Plugin** / page title action button styling so text stays readable in dark mode
- Fixed the WordPress Color Picker button styling by preserving the inline swatch background and styling only the text section
- Improved spacing, sizing and proportions to better match native WordPress admin UI defaults
- Extracted the settings page styles into a dedicated `assets/css/settings.css` file
- Reworked the settings page layout for cleaner spacing and more consistent component styling
- Improved plugin list styling, row actions, tables, notices, form controls, dashboard elements and navigation tabs
- Removed unwanted colored shadows / blue row highlight artifacts in plugin tables

### 0.0.2

- Redesigned settings page with card-based layout, page header with status badge and version display
- Added individual color customization for 9 dark mode colors via WordPress Color Picker
- Added "Restore Default Colors" button to reset all colors to the WordPress sidebar palette
- Added Custom CSS editor field to inject additional styles after the dark mode stylesheet
- All dark mode colors are now driven by CSS custom properties (`--adm-bg`, `--adm-card`, etc.)
- Base background color changed to `#1d2327` (native WordPress sidebar color)
- Fully optimized dark mode CSS: Admin Bar, Sidebar, Buttons, Forms, Tables, Notices, Gutenberg, Media, Screen Options, Dashboard Widgets
- Settings page itself adapts to dark mode when active

### 0.0.1

- Initial development release.

---

# Deutsch

Ein einfaches, schlankes Dark-Mode-Plugin für das WordPress Admin-Dashboard. Es ersetzt die helle Standard-Oberfläche durch ein sorgfältig gestaltetes dunkles Theme, unterstützt vollständige Farbanpassung über CSS-Variablen und enthält eine Auto-Dark-Mode-Funktion für die Kompatibilität mit Drittanbieter-Plugins.

## Beschreibung

DarkAdmin verwandelt das WordPress-Admin-Dashboard in eine angenehme, augenfreundliche dunkle Oberfläche. Das Plugin basiert vollständig auf CSS und ist dadurch schnell und nicht intrusiv. Alle Farben werden über CSS-Custom-Properties gesteuert und sind über eine dedizierte Einstellungsseite vollständig anpassbar — ohne Code-Änderungen. Eine optionale Auto-Dark-Mode-Funktion nutzt JavaScript, um Drittanbieter-Plugin-Bereiche dynamisch abzudecken, die vom eingebauten Stylesheet nicht erfasst werden.

### Funktionen

- Ein-Klick aktivieren/deaktivieren
- Leichtgewichtiges CSS-basiertes Admin-Theme
- Funktioniert auf allen Admin-Seiten
- Individuelle Farbanpassung über den WordPress Color Picker
- Unterstützung für eigenes Custom CSS mit eingebauten CSS-Variablen
- Token-basiertes Design-System für Hintergründe, Texte, Rahmen, Buttons und Statusfarben
- Auto Dark Mode: Verdunkelt automatisch helle Plugin-Hintergründe, die vom Stylesheet nicht abgedeckt werden

## Screenshots

### Settings Page – Standard (Dark Mode deaktiviert)
![DarkAdmin – Settings Page ohne Dark Mode](screenshot-1.png)

### Settings Page – Dark Mode aktiv
![DarkAdmin – Settings Page mit aktiviertem Dark Mode](screenshot-2.png)

### Dashboard – Standard (Dark Mode deaktiviert)
![DarkAdmin – Dashboard ohne Dark Mode](screenshot-3.png)

### Dashboard – Dark Mode aktiv
![DarkAdmin – Dashboard mit aktiviertem Dark Mode](screenshot-4.png)

## Installation

1. Lade den Plugin-Ordner nach `/wp-content/plugins/darkadmin/` hoch (oder installiere über „Plugins").
2. Aktiviere das Plugin in WordPress unter „Plugins".
3. Gehe zu **Einstellungen → DarkAdmin** und aktiviere es.

## FAQ

### Betrifft das das Frontend?

Nein. Es lädt nur CSS im `wp-admin`.

### Wo sind die Einstellungen?

**Einstellungen → DarkAdmin**

### Kann ich die Farben anpassen?

Ja. Das Plugin enthält mehrere Farbwähler für das komplette Admin-Theme und unterstützt zusätzlich eigenes Custom CSS.

### Was ist der Auto Dark Mode?

Ein optionaler zweiter Schalter, der JavaScript verwendet, um helle Hintergründe und dunklen Text von Drittanbieter-Plugins, die vom eingebauten Stylesheet nicht abgedeckt werden, dynamisch anzupassen. Erfordert, dass Dark Mode aktiv ist.

## Changelog

### 0.0.7

- Plugin-Upload-Formular (Plugins > Neu hinzufügen > Plugin hochladen) wird nun korrekt gestylt — WP-Core-CSS-Spezifitätskonflikt behoben
- Generische `.upload-plugin .wp-upload-form`-Selektoren durch `body.wp-admin div.upload-plugin > form.wp-upload-form` ersetzt, um WordPress-Standard `background: #f6f7f7` und `border: #c3c4c7` zu überschreiben
- Alle Sprachdateien (`.po`, `.pot`, `de_AT`, `de_DE`, `en_US`) korrigiert: ungültige `\uXXXX` Unicode-Escape-Sequenzen durch native UTF-8-Zeichen ersetzt, die `msgfmt` fatal errors verursacht haben

### 0.0.6

- Text Domain von `darkadmin` auf `darkadmin-dark-mode-for-adminpanel` aktualisiert (entspricht dem wordpress.org Plugin-Slug)
- `Plugin URI` auf `https://wordpress.org/plugins/darkadmin-dark-mode-for-adminpanel/` aktualisiert
- Alle Sprachdateien (`.po`, `.pot`) auf neue Text Domain aktualisiert

### 0.0.5

- Plugin von "WP Admin Dark Mode" zu "DarkAdmin - Dark Mode for Adminpanel" umbenannt
- Hauptdatei von `wp-admin-dark-mode.php` zu `darkadmin.php` umbenannt
- CSS-Datei von `wp-admin-dark.css` zu `darkadmin-dark.css` umbenannt
- Text-Domain von `wp-admin-dark-mode` auf `darkadmin` aktualisiert
- Alle Sprachdateien (`de_AT`, `de_DE`, `en_US`, `.pot`) auf neue `darkadmin` Text-Domain aktualisiert
- Einstellungen-Menü-Slug von `wp-admin-dark-mode` auf `darkadmin` aktualisiert
- Plugin-Ordner-Referenz in der Readme auf `/wp-content/plugins/darkadmin/` aktualisiert

### 0.0.4

- Auto Dark Mode hinzugefügt: optionaler JS-Pass, der helle Hintergründe und dunklen Text unbekannter Plugins dynamisch anpasst (WCAG-Luminanzberechnung)
- Auto Dark Mode verwendet einen MutationObserver, um auch AJAX-geladene Inhalte zu verarbeiten
- Farbfeld-Anzeige des Color Pickers korrigiert: `background-color` von `.wp-color-result` im CSS entfernt, damit WPs Inline-Style das Farbfeld korrekt anzeigt
- Alle Übersetzungsdateien (`de_AT`, `de_DE`, `en_US`, `.pot`) mit neuen Auto Dark Mode Strings aktualisiert

### 0.0.3

- Komplettes CSS-Refactoring mit token-basiertem Design-System für Hintergründe, Flächen, Rahmen, Texte, Links, Buttons und Statusfarben
- Neue anpassbare Farbvariablen hinzugefügt: `surface1`, `surface2`, `surface3`, `text_soft` und `danger`
- Anzahl der anpassbaren Farb-Tokens von 9 auf 13 erhöht
- Styling des **„Plugin hinzufügen"**-Buttons bzw. der Seitentitel-Actions korrigiert, damit der Text im Dark Mode lesbar bleibt
- WordPress Color Picker korrekt gefixt
- Abstände, Größen und Proportionen verbessert
- Styles der Einstellungsseite in eine eigene Datei `assets/css/settings.css` ausgelagert
- Layout der Einstellungsseite überarbeitet
- Plugin-Liste, Row-Actions, Tabellen, Notices, Formularelemente weiter optimiert
- Unerwünschte farbige Schatten in Plugin-Tabellen entfernt

### 0.0.2

- Einstellungsseite komplett neu gestaltet: Card-Layout, Page-Header mit Status-Badge und Versionsanzeige
- Individuelle Farbanpassung für 9 Dark-Mode-Farben über den WordPress Color Picker
- Schaltfläche „Standardfarben wiederherstellen"
- Custom-CSS-Editor hinzugefügt
- Alle Dark-Mode-Farben basieren nun auf CSS-Custom-Properties
- Hintergrundfarbe auf `#1d2327` geändert
- Dark-Mode-CSS vollständig überarbeitet
- Einstellungsseite passt sich selbst an den Dark Mode an

### 0.0.1

- Erste Development-Version.
