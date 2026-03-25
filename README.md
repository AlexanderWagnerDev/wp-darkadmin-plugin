# DarkAdmin – Dark Mode for Adminpanel

[![WordPress](https://img.shields.io/badge/WordPress-6.7%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPLv2%2B-green)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-0.1.3-orange)](https://github.com/AlexanderWagnerDev/wp-darkadmin-plugin)

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

1. Upload the plugin folder to `/wp-content/plugins/darkadmin-dark-mode-for-adminpanel/`
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

### 0.1.3
- Raised minimum WordPress version to 6.7
- Raised minimum PHP version requirement to 8.0 (already required by existing use of `str_starts_with`, `str_contains` and named arguments)
- Added defer loading strategy to `darkadmin-settings-js` and `darkadmin-auto-darken` via the `strategy` argument introduced in WordPress 6.3
- Fixed: replaced inline `echo '<script>'` in `settings-page.php` with `wp_add_inline_script()`
- Fixed: replaced anonymous arrow function sanitize callbacks in `register_setting()` with named functions `darkadmin_sanitize_bool()`, `darkadmin_sanitize_user_ids()` and `darkadmin_sanitize_preset()`
- Fixed: removed direct `$_POST` access in `darkadmin_sanitize_colors()` and `darkadmin_sanitize_layout()`; preset value now read from `$input` array
- Fixed: added late escaping via `wp_strip_all_tags()` to both `wp_add_inline_style()` calls for `$vars` and `$custom`
- Fixed: renamed generic JS object names `admData` and `admI18n` to `darkadminData` and `darkadminI18n` in `enqueue.php` and `settings.js`
- Added i18n string `"Copied!"` to `enqueue.php` via `wp_localize_script` (`darkadminI18n.copied`)
- Fixed: replaced hardcoded `'Copied!'` string in `settings.js` `initVarCopy()` with `darkadminI18n.copied` for full translateability
- Fixed: replaced `innerHTML` with `textContent` in `initVarCopy()` to prevent potential XSS
- Updated all language files (`.pot`, `de_AT`, `de_DE`, `en_US`): added `Copied!` / `Kopiert!` translation, bumped version to 0.1.3, updated timestamps

### 0.1.2
- Added dedicated Sidebar color group with three new tokens: Sidebar Background (--adm-sidebar-bg), Sidebar Active Item (--adm-sidebar-active), Sidebar Text (--adm-sidebar-text)
- Added sidebar token translations to all language files (de_AT, de_DE, en_US, .pot, .l10n.php)
- Added layout token system (spacing, radius, shadow) with per-preset defaults and settings UI
- Unified layout tokens across presets, added layout JS handlers, updated all language files
- Added `.adm-layout-grid` CSS: 4-column grid with responsive breakpoints and dark mode overrides
- Improved color picker swatch display in settings page
- Fixed `translators` comment and `phpcs:ignore` placement in `settings-page.php`
- Fixed: replaced `&amp;` HTML entity with literal UTF-8 ampersand in i18n strings (`settings-page.php`)
- Fixed: replaced PHP `\u2713` escape with literal UTF-8 checkmark character in admin notice strings
- Fixed: replaced `&#10003;` HTML entity with literal UTF-8 checkmark in preset button PHP and all `.po` files
- Fixed: replaced ASCII-escaped umlauts with proper UTF-8 characters in all language files, added missing msgids (checkmark "Active", em-dash in admin notice)
- Updated `darkadmin-dark.css` and `darkadmin-wp-modern.css`

### 0.1.1
- Fixed `uninstall.php`: corrected all option names from wrong `adm_` prefix to `darkadmin_` prefix so options are properly removed on plugin deletion

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

---

# DarkAdmin – Dark Mode für das Adminpanel

Ein einfaches, schlankes Dark-Mode-Plugin für das WordPress Admin-Dashboard mit vollständiger Farbanpassung und Auto-Dark-Mode-Unterstützung.

---

## Funktionen

- Ein-Klick aktivieren/deaktivieren
- Leichtgewichtiges CSS-basiertes Admin-Theme
- Funktioniert auf allen Admin-Seiten
- Individuelle Farbanpassung über den WordPress Color Picker
- Unterstützung für eigenes Custom CSS mit eingebauten CSS-Variablen
- Token-basiertes Design-System für Hintergründe, Texte, Rahmen, Buttons und Statusfarben
- Auto Dark Mode: Verdunkelt automatisch helle Plugin-Hintergründe, die vom Stylesheet nicht abgedeckt werden
- Preset-Themes: Wechsel zwischen Default (WP 6.x) und Modern (WP 7.0) Farbpaletten
- Benutzerspezifische Dark-Mode-Zugriffskontrolle (Einschließen / Ausschließen) mit Empty-State-UI wenn keine Nicht-Admin-Benutzer vorhanden sind
- Ausgeschlossene Seiten: Admin-Seiten angeben, auf denen Dark Mode nicht angewendet werden soll

---

## Installation

1. Lade den Plugin-Ordner nach `/wp-content/plugins/darkadmin-dark-mode-for-adminpanel/` hoch
2. Aktiviere das Plugin in WordPress unter **Plugins**
3. Gehe zu **Einstellungen > DarkAdmin** und aktiviere es

---

## Changelog

### 0.1.3
- Mindest-WordPress-Version auf 6.7 angehoben
- Mindest-PHP-Version auf 8.0 angehoben (bereits erforderlich durch Verwendung von `str_starts_with`, `str_contains` und Named Arguments)
- Defer-Ladestrategie für `darkadmin-settings-js` und `darkadmin-auto-darken` via `strategy`-Argument (eingeführt in WordPress 6.3) hinzugefügt
- Fix: Inline-`echo '<script>'` in `settings-page.php` durch `wp_add_inline_script()` ersetzt
- Fix: Anonyme Arrow-Function-Sanitize-Callbacks in `register_setting()` durch benannte Funktionen `darkadmin_sanitize_bool()`, `darkadmin_sanitize_user_ids()` und `darkadmin_sanitize_preset()` ersetzt
- Fix: Direkten `$_POST`-Zugriff in `darkadmin_sanitize_colors()` und `darkadmin_sanitize_layout()` entfernt; Preset-Wert wird nun aus dem `$input`-Array gelesen
- Fix: Spätes Escaping via `wp_strip_all_tags()` zu beiden `wp_add_inline_style()`-Aufrufen für `$vars` und `$custom` hinzugefügt
- Fix: Generische JS-Objektnamen `admData` und `admI18n` in `enqueue.php` und `settings.js` zu `darkadminData` und `darkadminI18n` umbenannt
- i18n-String `"Copied!"` in `enqueue.php` via `wp_localize_script` (`darkadminI18n.copied`) hinzugefügt
- Fix: Hardcodierten `'Copied!'`-String in `settings.js` `initVarCopy()` durch `darkadminI18n.copied` ersetzt für vollständige Übersetzbarkeit
- Fix: `innerHTML` in `initVarCopy()` durch `textContent` ersetzt (verhindert potenzielles XSS)
- Alle Sprachdateien aktualisiert (`.pot`, `de_AT`, `de_DE`, `en_US`): `Copied!` / `Kopiert!`-Übersetzung hinzugefügt, Version auf 0.1.3 angehoben, Zeitstempel aktualisiert

### 0.1.2
- Neue Sidebar-Farbgruppe hinzugefügt mit drei neuen Tokens: Sidebar-Hintergrund (--adm-sidebar-bg), Sidebar aktives Element (--adm-sidebar-active) und Sidebar-Text (--adm-sidebar-text)
- Sidebar-Token-Übersetzungen in alle Sprachdateien eingetragen (de_AT, de_DE, en_US, .pot, .l10n.php)
- Layout-Token-System hinzugefügt (Spacing, Radius, Shadow) mit per-Preset-Defaults und Settings-UI
- Layout-Tokens über Presets vereinheitlicht, Layout-JS-Handler hinzugefügt, alle Sprachdateien aktualisiert
- `.adm-layout-grid` CSS hinzugefügt: 4-Spalten-Grid mit responsiven Breakpoints und Dark-Mode-Overrides
- Darstellung der Color-Picker-Swatches in der Einstellungsseite verbessert
- Fix: `translators`-Kommentar und `phpcs:ignore` in `settings-page.php` korrekt gesetzt
- Fix: `&amp;` HTML-Entity durch direktes UTF-8-Ampersand in i18n-Strings ersetzt (`settings-page.php`)
- Fix: PHP `\u2713`-Escape durch direktes UTF-8-Häkchen in Admin-Notice-Strings ersetzt
- Fix: `&#10003;` HTML-Entity durch direktes UTF-8-Häkchen im Preset-Button-PHP und allen `.po`-Dateien ersetzt
- Fix: ASCII-escaped Umlaute durch korrekte UTF-8-Zeichen in allen Sprachdateien ersetzt, fehlende msgids ergänzt (Häkchen „Active", Em-Dash in Admin-Notice)
- `darkadmin-dark.css` und `darkadmin-wp-modern.css` aktualisiert

### 0.1.1
- Behoben: `uninstall.php` korrigiert — alle Optionsnamen vom falschen `adm_`-Prefix auf den korrekten `darkadmin_`-Prefix geändert, damit Optionen beim Deinstallieren sauber entfernt werden

### 0.1.0
- Unterstützung für ausgeschlossene Seiten in den Einstellungen hinzugefügt
- Benutzerzugriffskontrolle hinzugefügt (Benutzer ein-/ausschließen)
- Voreingestellte Designs hinzugefügt (Standard und Modern)
- Kritische JavaScript-Fehler in der Voreinstellungs- und Zurücksetzungsfunktion behoben
- Fehlende schließende geschweifte Klammer in `initPaletteIO()` importFile-Block in `settings.js` behoben
- XSS-Sicherheitslücke in `printf`-Ausgabe (`settings-page.php`) behoben
- Unicode-Escapes in Sprachdateien behoben: `\uXXXX`-Sequenzen durch direkte UTF-8-Zeichen ersetzt
- `admI18n` JS-Lokalisierung via `wp_localize_script` für übersetzte UI-Strings hinzugefügt
- Redundantes `wp-color-picker` Script-Enqueue entfernt
- `.l10n.php` Sprach-Cache-Dateien für alle Locales (`de_AT`, `de_DE`, `en_US`) mit ABSPATH-Schutz hinzugefügt
- Hexadezimalvalidierung für JSON-Palettenimporte hinzugefügt
- Dokumentation für neue Funktionen aktualisiert

### 0.0.10
- Themes-Bereich erweitert: Dark-Styling für `.theme-browser .theme .theme-name`, `.theme-overlay .theme-actions`, `.theme-overlay .theme-tags`, `.theme-overlay .theme-header .theme-title`, `.theme-overlay .theme-author`, `.theme-overlay .theme-version` und `.theme-overlay .theme-rating .star-rating .star`
- Theme-Editor / Template-Side-Bereich hinzugefügt: Dark-Styling für `#templateside > ul`, `.importer-title` und `.color-option.selected` / `.color-option:hover`
- `.cm-error`-Hintergrund-Deckkraft von `.15` auf `.05` reduziert für subtileres Fehler-Highlighting in CodeMirror
- Alle Änderungen in `darkadmin-dark.css` und `darkadmin-wp-modern.css` umgesetzt
- Ungültige Steuerzeichen in allen Sprachdateien (`de_AT`, `de_DE`, `en_US`, `.pot`) behoben
- Benutzerzugriff: Einschließen- und Ausschließen-Optionen werden nun ausgegraut und nicht klickbar, wenn keine Nicht-Administrator-Benutzer vorhanden sind
- Benutzerzugriff: Einfachen Text-Fallback durch gestalteten Empty-State-Block ersetzt
- i18n: fehlenden String „Keine Nicht-Administrator-Benutzer gefunden" in allen Sprachdateien ergänzt

### 0.0.9
- Preset-Themes hinzugefügt: Default (klassisches WP 6.x Dark) und Modern (WP 7.0 Tiefblau)
- Jedes Preset hat eine eigene CSS-Datei, die dynamisch geladen wird
- `adm_preset`-Option mit Live-Preset-Wechsel hinzugefügt
- Benutzerspezifischer Dark Mode mit User-Access-Karte
- Live-Farbvorschau: Farbwähler-Änderungen aktualisieren CSS-Variablen sofort ohne Speichern
- Export / Import der Farbpalette als JSON-Datei
- Eigener CSS-Sanitizer (`adm_sanitize_custom_css`)
- CSS-Cache-Busting auf Basis des md5-Hashes der aktuellen Farbwerte
- Plugin in modulare Includes aufgeteilt
- `uninstall.php` hinzugefügt
- Farbwähler nach Kategorien gruppiert
- Farb-Tokens von 23 auf 34 erweitert

### 0.0.8
- Unsichtbaren Text in `.widefat`-Tabellen behoben

### 0.0.7
- Version in den `darkadmin-dark.css` Header-Kommentar eingefügt

### 0.0.6
- Text Domain auf `darkadmin-dark-mode-for-adminpanel` aktualisiert
- Plugin URI auf wordpress.org aktualisiert
- Alle Sprachdateien auf neue Text Domain aktualisiert

### 0.0.5
- Plugin von „WP Admin Dark Mode" zu „DarkAdmin - Dark Mode for Adminpanel" umbenannt
- Hauptdateien, CSS-Dateien, Text-Domain und Menü-Slug umbenannt
- Alle Sprachdateien aktualisiert

### 0.0.4
- Auto Dark Mode mit WCAG-Luminanzberechnungen hinzugefügt
- Auto Dark Mode verwendet MutationObserver für AJAX-geladene Inhalte
- Farbfeld-Anzeige des Color Pickers korrigiert
- Alle Übersetzungsdateien aktualisiert

### 0.0.3
- Komplettes CSS-Refactoring mit token-basiertem Design-System
- Neue anpassbare Farbvariablen hinzugefügt
- Anzahl der Farb-Tokens von 9 auf 13 erhöht
- Diverse Styling-Fixes und Layout-Verbesserungen

### 0.0.2
- Einstellungsseite komplett neu gestaltet mit Card-Layout
- Individuelle Farbanpassung über den WordPress Color Picker
- Custom-CSS-Editor hinzugefügt
- Alle Dark-Mode-Farben basieren nun auf CSS-Custom-Properties

### 0.0.1
- Erste Development-Version.

---

## Lizenz

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

---

*Entwickelt von [AlexanderWagnerDev](https://alexanderwagnerdev.com)*
