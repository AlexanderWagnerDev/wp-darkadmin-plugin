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
| **Requires at least** | 6.3 |
| **Tested up to** | 6.9 |
| **Requires PHP** | 7.4 |
| **Stable tag** | 0.0.10 |
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
- Preset Themes: choose between Default (WP 6.x) and Modern (WP 7.0) color palettes

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

1. Upload the plugin folder to `/wp-content/plugins/darkadmin-dark-mode-for-adminpanel/` (or install via the Plugins screen).
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

### 0.0.10

- Extended Themes section: added dark styling for `.theme-browser .theme .theme-name`, `.theme-overlay .theme-actions`, `.theme-overlay .theme-tags`, `.theme-overlay .theme-header .theme-title`, `.theme-overlay .theme-author`, `.theme-overlay .theme-version` and `.theme-overlay .theme-rating .star-rating .star`
- Added Theme Editor / Template Side section: dark styling for `#templateside > ul`, `.importer-title` and `.color-option.selected` / `.color-option:hover`
- Reduced `.cm-error` background opacity from `.15` to `.05` for a more subtle error highlight in CodeMirror
- All changes applied to both `darkadmin-dark.css` and `darkadmin-wp-modern.css`

### 0.0.9

- Added Preset Themes: choose between Default (WP 6.x classic dark) and Modern (WP 7.0 deep blue, glassmorphism-inspired) color palettes
- Each preset ships with its own CSS file (`darkadmin-dark.css` / `darkadmin-modern.css`) loaded dynamically based on the active preset
- Added `adm_preset` option with live preset switching on the settings page
- Added per-user Dark Mode: administrators always see dark mode, non-admin users can be individually enabled via a new User Access card
- Added live color preview: color picker changes update CSS variables instantly without saving
- Added Export / Import palette as JSON file
- Added custom CSS sanitizer (`adm_sanitize_custom_css`) — preserves valid CSS while stripping dangerous HTML/PHP tags
- Added CSS cache-busting based on `md5` hash of current color values
- Refactored plugin into modular includes: `defaults.php`, `user-settings.php`, `enqueue.php`, `settings-page.php`
- Added `uninstall.php` to clean up all options on plugin removal
- Color pickers now grouped by category (Backgrounds, Surfaces, Borders, Text, Links, Brand, CodeMirror) on the settings page
- Expanded color tokens from 23 to 34 (new: `bg_bar`, `bg_deep`, `bg_darker`, `table_alt`, `plugin_inactive`, `border_hover`, `text_on_primary`, `link_hover`, `primary_hover`, `cm_keyword`–`cm_bracket`)

### 0.0.8

- Fixed invisible text in `.widefat` tables (update-core.php and similar pages): override WP core rule `.widefat ol, .widefat p, .widefat ul { color: #2c3338 }` with dark theme color token

### 0.0.7

- Fixed Plugin Upload Form (Plugins > Add New > Upload Plugin) not being styled correctly due to WP core CSS specificity conflict
- Replaced generic `.upload-plugin .wp-upload-form` selectors with scoped `body.wp-admin div.upload-plugin > form.wp-upload-form` to override WordPress default `background: #f6f7f7` and `border: #c3c4c7`

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
- Preset-Themes: Wechsel zwischen Default (WP 6.x) und Modern (WP 7.0) Farbpaletten

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

1. Lade den Plugin-Ordner nach `/wp-content/plugins/darkadmin-dark-mode-for-adminpanel/` hoch (oder installiere über „Plugins").
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

### 0.0.10

- Themes-Bereich erweitert: Dark-Styling für `.theme-browser .theme .theme-name`, `.theme-overlay .theme-actions`, `.theme-overlay .theme-tags`, `.theme-overlay .theme-header .theme-title`, `.theme-overlay .theme-author`, `.theme-overlay .theme-version` und `.theme-overlay .theme-rating .star-rating .star`
- Theme-Editor / Template-Side-Bereich hinzugefügt: Dark-Styling für `#templateside > ul`, `.importer-title` und `.color-option.selected` / `.color-option:hover`
- `.cm-error`-Hintergrund-Deckkraft von `.15` auf `.05` reduziert für ein subtileres Fehler-Highlighting in CodeMirror
- Alle Änderungen in `darkadmin-dark.css` und `darkadmin-wp-modern.css` umgesetzt

### 0.0.9

- Preset-Themes hinzugefügt: Wechsel zwischen Default (klassisches WP 6.x Dark) und Modern (WP 7.0 Tiefblau, Glassmorphism) Farbpaletten
- Jedes Preset hat eine eigene CSS-Datei (`darkadmin-dark.css` / `darkadmin-modern.css`), die dynamisch geladen wird
- `adm_preset`-Option mit Live-Preset-Wechsel auf der Einstellungsseite hinzugefügt
- Benutzerspezifischer Dark Mode: Admins sehen immer Dark Mode, Nicht-Admin-Benutzer können über eine neue User-Access-Karte einzeln aktiviert werden
- Live-Farbvorschau: Farbwähler-Änderungen aktualisieren CSS-Variablen sofort ohne Speichern
- Export / Import der Farbpalette als JSON-Datei
- Eigener CSS-Sanitizer (`adm_sanitize_custom_css`) — erhält gültiges CSS, entfernt gefährliche HTML/PHP-Tags
- CSS-Cache-Busting auf Basis des `md5`-Hashes der aktuellen Farbwerte
- Plugin in modulare Includes aufgeteilt: `defaults.php`, `user-settings.php`, `enqueue.php`, `settings-page.php`
- `uninstall.php` hinzugefügt — entfernt alle Optionen beim Deinstallieren
- Farbwähler auf der Einstellungsseite nach Kategorien gruppiert (Hintergründe, Flächen, Rahmen, Text, Links, Brand, CodeMirror)
- Farb-Tokens von 23 auf 34 erweitert (neu: `bg_bar`, `bg_deep`, `bg_darker`, `table_alt`, `plugin_inactive`, `border_hover`, `text_on_primary`, `link_hover`, `primary_hover`, `cm_keyword`–`cm_bracket`)

### 0.0.8

- Unsichtbaren Text in `.widefat`-Tabellen behoben (update-core.php und ähnliche Seiten): WP-Core-Regel `.widefat ol, .widefat p, .widefat ul { color: #2c3338 }` wird nun mit dem Dark-Theme-Farbtoken überschrieben

### 0.0.7

- Plugin-Upload-Formular (Plugins > Neu hinzufügen > Plugin hochladen) wird nun korrekt gestylt — WP-Core-CSS-Spezifitätskonflikt behoben
- Generische `.upload-plugin .wp-upload-form`-Selektoren durch `body.wp-admin div.upload-plugin > form.wp-upload-form` ersetzt, um WordPress-Standard `background: #f6f7f7` und `border: #c3c4c7` zu überschreiben

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
- Plugin-Ordner-Referenz in der Readme auf `/wp-content/plugins/darkadmin-dark-mode-for-adminpanel/` aktualisiert

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
