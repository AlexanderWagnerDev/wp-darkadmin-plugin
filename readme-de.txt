=== DarkAdmin - Dark Mode for Adminpanel ===
Contributors: alexanderwagnerdev
Tags: dark mode, admin, dashboard, ui, accessibility
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.0.10
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Ein einfaches, schlankes Dark-Mode-Plugin für das WordPress Admin-Dashboard mit vollständiger Farbanpassung und Auto-Dark-Mode-Unterstützung.

== Beschreibung ==
DarkAdmin verwandelt das WordPress-Admin-Dashboard in eine angenehme, augenfreundliche dunkle Oberfläche. Das Plugin basiert vollständig auf CSS und ist dadurch schnell und nicht intrusiv. Alle Farben werden über CSS-Custom-Properties gesteuert und sind über eine dedizierte Einstellungsseite vollständig anpassbar — ohne Code-Änderungen. Eine optionale Auto-Dark-Mode-Funktion nutzt JavaScript, um Drittanbieter-Plugin-Bereiche dynamisch abzudecken, die vom eingebauten Stylesheet nicht erfasst werden.

Funktionen:
* Ein-Klick aktivieren/deaktivieren
* Leichtgewichtiges CSS-basiertes Admin-Theme
* Funktioniert auf allen Admin-Seiten
* Individuelle Farbanpassung über den WordPress Color Picker
* Unterstützung für eigenes Custom CSS mit eingebauten CSS-Variablen
* Token-basiertes Design-System für Hintergründe, Texte, Rahmen, Buttons und Statusfarben
* Auto Dark Mode: Verdunkelt automatisch helle Plugin-Hintergründe, die vom Stylesheet nicht abgedeckt werden
* Preset-Themes: Wechsel zwischen Default (WP 6.x) und Modern (WP 7.0) Farbpaletten

== Installation ==
1. Lade den Plugin-Ordner nach `/wp-content/plugins/darkadmin/` hoch (oder installiere über „Plugins").
2. Aktiviere das Plugin in WordPress unter „Plugins".
3. Gehe zu Einstellungen → DarkAdmin und aktiviere es.

== Häufige Fragen ==
= Betrifft das das Frontend? =
Nein. Es lädt nur CSS im wp-admin.

= Wo sind die Einstellungen? =
Einstellungen → DarkAdmin

= Kann ich die Farben anpassen? =
Ja. Das Plugin enthält mehrere Farbwähler für das komplette Admin-Theme und unterstützt zusätzlich eigenes Custom CSS.

= Was ist der Auto Dark Mode? =
Ein optionaler zweiter Schalter, der JavaScript verwendet, um helle Hintergründe und dunklen Text von Drittanbieter-Plugins, die vom eingebauten Stylesheet nicht abgedeckt werden, dynamisch anzupassen. Erfordert, dass Dark Mode aktiv ist.

== Screenshots ==
1. Settings Page – Standard (Dark Mode deaktiviert)
2. Settings Page – Dark Mode aktiv
3. Dashboard – Standard (Dark Mode deaktiviert)
4. Dashboard – Dark Mode aktiv

== Changelog ==
= 0.0.10 =
* Themes-Bereich erweitert: Dark-Styling für .theme-browser .theme .theme-name, .theme-overlay .theme-actions, .theme-overlay .theme-tags, .theme-overlay .theme-header .theme-title, .theme-overlay .theme-author, .theme-overlay .theme-version und .theme-overlay .theme-rating .star-rating .star
* Theme-Editor / Template-Side-Bereich hinzugefügt: Dark-Styling für #templateside > ul, .importer-title und .color-option.selected / .color-option:hover
* .cm-error-Hintergrund-Deckkraft von .15 auf .05 reduziert für subtileres Fehler-Highlighting in CodeMirror
* Alle Änderungen in darkadmin-dark.css und darkadmin-wp-modern.css umgesetzt

= 0.0.9 =
* Preset-Themes hinzugefügt: Wechsel zwischen Default (klassisches WP 6.x Dark) und Modern (WP 7.0 Tiefblau, Glassmorphism) Farbpaletten
* Jedes Preset hat eine eigene CSS-Datei (darkadmin-dark.css / darkadmin-modern.css), die dynamisch geladen wird
* adm_preset-Option mit Live-Preset-Wechsel auf der Einstellungsseite hinzugefügt
* Benutzerspezifischer Dark Mode: Admins sehen immer Dark Mode, Nicht-Admin-Benutzer können über eine neue User-Access-Karte einzeln aktiviert werden
* Live-Farbvorschau: Farbwähler-Änderungen aktualisieren CSS-Variablen sofort ohne Speichern
* Export / Import der Farbpalette als JSON-Datei
* Eigener CSS-Sanitizer (adm_sanitize_custom_css) — erhält gültiges CSS, entfernt gefährliche HTML/PHP-Tags
* CSS-Cache-Busting auf Basis des md5-Hashes der aktuellen Farbwerte
* Plugin in modulare Includes aufgeteilt: defaults.php, user-settings.php, enqueue.php, settings-page.php
* uninstall.php hinzugefügt — entfernt alle Optionen beim Deinstallieren
* Farbwähler auf der Einstellungsseite nach Kategorien gruppiert (Hintergründe, Flächen, Rahmen, Text, Links, Brand, CodeMirror)
* Farb-Tokens von 23 auf 34 erweitert

= 0.0.8 =
* Unsichtbaren Text in .widefat-Tabellen behoben (update-core.php und ähnliche Seiten): WP-Core-Regel `.widefat ol, .widefat p, .widefat ul { color: #2c3338 }` wird nun mit dem Dark-Theme-Farbtoken überschrieben

= 0.0.7 =
* Version in den darkadmin-dark.css Header-Kommentar eingefügt

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
