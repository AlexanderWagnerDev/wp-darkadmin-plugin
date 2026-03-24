=== DarkAdmin - Dark Mode for Adminpanel ===
Contributors: alexanderwagnerdev
Tags: dark mode, admin, dashboard, ui, accessibility
Requires at least: 6.3
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 0.1.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Ein einfaches, schlankes Dark-Mode-Plugin fuer das WordPress Admin-Dashboard mit vollstaendiger Farbanpassung und Auto-Dark-Mode-Unterstuetzung.

== Beschreibung ==
DarkAdmin verwandelt das WordPress-Admin-Dashboard in eine angenehme, augenfreundliche dunkle Oberflaeche. Das Plugin basiert vollstaendig auf CSS und ist dadurch schnell und nicht intrusiv. Alle Farben werden ueber CSS-Custom-Properties gesteuert und sind ueber eine dedizierte Einstellungsseite vollstaendig anpassbar -- ohne Code-Aenderungen. Eine optionale Auto-Dark-Mode-Funktion nutzt JavaScript, um Drittanbieter-Plugin-Bereiche dynamisch abzudecken, die vom eingebauten Stylesheet nicht erfasst werden.

Funktionen:
* Ein-Klick aktivieren/deaktivieren
* Leichtgewichtiges CSS-basiertes Admin-Theme
* Funktioniert auf allen Admin-Seiten
* Individuelle Farbanpassung ueber den WordPress Color Picker
* Unterstuetzung fuer eigenes Custom CSS mit eingebauten CSS-Variablen
* Token-basiertes Design-System fuer Hintergruende, Texte, Rahmen, Buttons und Statusfarben
* Auto Dark Mode: Verdunkelt automatisch helle Plugin-Hintergruende, die vom Stylesheet nicht abgedeckt werden
* Preset-Themes: Wechsel zwischen Default (WP 6.x) und Modern (WP 7.0) Farbpaletten
* Benutzerspezifische Dark-Mode-Zugriffskontrolle (Einschliessen / Ausschliessen) mit Empty-State-UI wenn keine Nicht-Admin-Benutzer vorhanden sind
* Ausgeschlossene Seiten: Admin-Seiten angeben, auf denen Dark Mode nicht angewendet werden soll

== Installation ==
1. Lade den Plugin-Ordner nach `/wp-content/plugins/darkadmin/` hoch (oder installiere ueber "Plugins").
2. Aktiviere das Plugin in WordPress unter "Plugins".
3. Gehe zu Einstellungen -> DarkAdmin und aktiviere es.

== Haeufige Fragen ==
= Betrifft das das Frontend? =
Nein. Es laedt nur CSS im wp-admin.

= Wo sind die Einstellungen? =
Einstellungen -> DarkAdmin

= Kann ich die Farben anpassen? =
Ja. Das Plugin enthaelt mehrere Farbwaehler fuer das komplette Admin-Theme und unterstuetzt zusaetzlich eigenes Custom CSS.

= Was ist der Auto Dark Mode? =
Ein optionaler zweiter Schalter, der JavaScript verwendet, um helle Hintergruende und dunklen Text von Drittanbieter-Plugins, die vom eingebauten Stylesheet nicht abgedeckt werden, dynamisch anzupassen. Erfordert, dass Dark Mode aktiv ist.

== Screenshots ==
1. Settings Page - Standard (Dark Mode deaktiviert)
2. Settings Page - Dark Mode aktiv
3. Dashboard - Standard (Dark Mode deaktiviert)
4. Dashboard - Dark Mode aktiv

== Changelog ==
= 0.1.2 =
* Neue Sidebar-Farbgruppe hinzugefuegt mit drei neuen Tokens: Sidebar-Hintergrund (--adm-sidebar-bg), Sidebar aktives Element (--adm-sidebar-active) und Sidebar-Text (--adm-sidebar-text)
* Sidebar-Token-Uebersetzungen in alle Sprachdateien eingetragen (de_AT, de_DE, en_US, .pot, .l10n.php)

= 0.1.1 =
* Behoben: uninstall.php korrigiert -- alle Optionsnamen vom falschen adm_-Prefix auf den korrekten darkadmin_-Prefix geaendert, damit Optionen beim Deinstallieren sauber entfernt werden

= 0.1.0 =
* Unterstuetzung fuer ausgeschlossene Seiten in den Einstellungen hinzugefuegt
* Benutzerzugriffskontrolle hinzugefuegt (Benutzer ein-/ausschliessen)
* Voreingestellte Designs hinzugefuegt (Standard und Modern)
* Kritische JavaScript-Fehler in der Voreinstellungs- und Zuruecksetzungsfunktion behoben
* Fehlende schliessende geschweifte Klammer in initPaletteIO() importFile-Block in settings.js behoben
* XSS-Sicherheitsluecke in printf-Ausgabe (settings-page.php) behoben
* Unicode-Escapes in Sprachdateien behoben: \uXXXX-Sequenzen durch direkte UTF-8-Zeichen ersetzt
* admI18n JS-Lokalisierung via wp_localize_script fuer uebersetzte UI-Strings hinzugefuegt
* Redundantes wp-color-picker Script-Enqueue entfernt
* .l10n.php Sprach-Cache-Dateien fuer alle Locales (de_AT, de_DE, en_US) mit ABSPATH-Schutz hinzugefuegt
* Hexadezimalvalidierung fuer JSON-Palettenimporte hinzugefuegt
* Dokumentation fuer neue Funktionen aktualisiert

= 0.0.10 =
* Themes-Bereich erweitert: Dark-Styling fuer .theme-browser .theme .theme-name, .theme-overlay .theme-actions, .theme-overlay .theme-tags, .theme-overlay .theme-header .theme-title, .theme-overlay .theme-author, .theme-overlay .theme-version und .theme-overlay .theme-rating .star-rating .star
* Theme-Editor / Template-Side-Bereich hinzugefuegt: Dark-Styling fuer #templateside > ul, .importer-title und .color-option.selected / .color-option:hover
* .cm-error-Hintergrund-Deckkraft von .15 auf .05 reduziert fuer subtileres Fehler-Highlighting in CodeMirror
* Alle Aenderungen in darkadmin-dark.css und darkadmin-wp-modern.css umgesetzt
* Ungueltigen Steuerzeichen in allen Sprachdateien (de_AT, de_DE, en_US, .pot) behoben: \uXXXX-Unicode-Escapes durch direkte UTF-8-Zeichen ersetzt
* Benutzerzugriff: Einschliessen- und Ausschliessen-Optionen werden nun ausgegraut und nicht klickbar, wenn keine Nicht-Administrator-Benutzer vorhanden sind
* Benutzerzugriff: Einfachen Text-Fallback durch gestalteten Empty-State-Block ersetzt
* i18n: fehlenden String ergaenzt in .pot, de_AT, de_DE und en_US Sprachdateien

= 0.0.9 =
* Preset-Themes hinzugefuegt: Wechsel zwischen Default (klassisches WP 6.x Dark) und Modern (WP 7.0 Tiefblau, Glassmorphism)
* Jedes Preset hat eine eigene CSS-Datei, die dynamisch geladen wird
* adm_preset-Option mit Live-Preset-Wechsel auf der Einstellungsseite hinzugefuegt
* Benutzerspezifischer Dark Mode: Admins sehen immer Dark Mode, Nicht-Admin-Benutzer koennen einzeln aktiviert werden
* Live-Farbvorschau: Farbwaehler-Aenderungen aktualisieren CSS-Variablen sofort ohne Speichern
* Export / Import der Farbpalette als JSON-Datei
* Eigener CSS-Sanitizer -- erhaelt gueltiges CSS, entfernt gefaehrliche HTML/PHP-Tags
* CSS-Cache-Busting auf Basis des md5-Hashes der aktuellen Farbwerte
* Plugin in modulare Includes aufgeteilt: defaults.php, user-settings.php, enqueue.php, settings-page.php
* uninstall.php hinzugefuegt -- entfernt alle Optionen beim Deinstallieren
* Farbwaehler nach Kategorien gruppiert (Hintergruende, Flaechen, Rahmen, Text, Links, Brand, CodeMirror)
* Farb-Tokens von 23 auf 34 erweitert

= 0.0.8 =
* Unsichtbaren Text in .widefat-Tabellen behoben

= 0.0.7 =
* Version in den darkadmin-dark.css Header-Kommentar eingefuegt

= 0.0.6 =
* Text Domain aktualisiert auf darkadmin-dark-mode-for-adminpanel
* Plugin URI aktualisiert
* Alle Sprachdateien auf neue Text Domain aktualisiert

= 0.0.5 =
* Plugin umbenannt zu DarkAdmin - Dark Mode for Adminpanel
* Hauptdatei umbenannt zu darkadmin.php
* CSS-Datei umbenannt zu darkadmin-dark.css
* Text-Domain aktualisiert
* Einstellungen-Menue-Slug aktualisiert

= 0.0.4 =
* Auto Dark Mode hinzugefuegt
* Auto Dark Mode verwendet einen MutationObserver fuer AJAX-geladene Inhalte
* Farbfeld-Anzeige des Color Pickers korrigiert
* Alle Uebersetzungsdateien aktualisiert

= 0.0.3 =
* Komplettes CSS-Refactoring mit token-basiertem Design-System
* Neue anpassbare Farbvariablen hinzugefuegt
* Anzahl der Farb-Tokens von 9 auf 13 erhoeht
* Diverse Styling-Fixes

= 0.0.2 =
* Einstellungsseite komplett neu gestaltet
* Individuelle Farbanpassung ueber den WordPress Color Picker
* Custom-CSS-Editor hinzugefuegt
* Alle Dark-Mode-Farben basieren nun auf CSS-Custom-Properties

= 0.0.1 =
* Erste Development-Version.
