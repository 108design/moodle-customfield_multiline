<?php
/**
 * This file is part of Moodle - https://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle. If not, see <https://www.gnu.org/licenses/>.
 *
 */
// Copyright (C) 2026 Andreas Giesen. See LICENSE.md.
/**
 * @package customfield_multiline
 * @copyright 2026 Andreas Giesen <andreas@108design.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['defaultvalue_help'] = 'Gib Klartext ein. Enter setzt einen festen Zeilenumbruch; Leerzeilen bleiben erhalten. Der Standard gilt nur, solange kein Kurswert gespeichert wurde. Ein ausdrücklich leer gespeicherter Wert bleibt leer. Schrift und Ausrichtung legst du in der Zertifikatvorlage fest.';
$string['value_help'] = 'Wähle darunter den Textmodus für diesen Kurs. Klartext verwendet Enter als festen Zeilenumbruch und zeigt HTML-Tags wörtlich. Begrenztes HTML verwendet br oder p für Umbrüche. Schrift, Größe, Farbe und Breite legst du in der Zertifikatvorlage fest. Die Ausrichtung kannst du darunter für diesen Kurs überschreiben.';
$string['adminhelp_overview_article'] = '<p>Wähle beim Anlegen eines Kursfelds Certificate Multitool – Custom Field. Trage in den Kurseinstellungen eine oder mehrere Zeilen ein. Enter setzt einen festen Zeilenumbruch; eine Leerzeile fügt Abstand ein. Im Modus Klartext werden HTML-Tags als gewöhnlicher Text behandelt. Unter Textmodus kannst du alternativ Begrenztes HTML aktivieren.</p><p>Schrift, Breite und Position stammen aus der Zertifikatvorlage. Die horizontale Ausrichtung und den vertikalen Anker kannst du pro Kurs überschreiben. Aus Vorlage übernehmen verwendet die jeweilige Voreinstellung des Elements. Mit Bereichshöhe richtet Oben oder Unten den Text am jeweiligen Rand des festgelegten Bereichs aus. Ohne Bereichshöhe setzt Oben die Oberkante und Unten die Unterkante des Textes an Position Y. Mitte zentriert den gesamten Textblock vertikal im festgelegten Bereich. Dafür muss im Zertifikatselement eine Bereichshöhe gesetzt sein.</p>';
$string['htmlmode_help'] = 'Wähle den Modus für diesen Kurs und dieses Feld. Neue Felder beginnen mit Klartext; bestehende Werte behalten beim Upgrade ihren bisherigen Modus. Klartext: Enter setzt feste Zeilenumbrüche und HTML wird als Text angezeigt. Begrenztes HTML wertet ausschließlich strong, b, em, i, u, ul, ol, li, br und p aus. Attribute, CSS, Bilder und andere Einbettungen werden nicht übernommen. Gib HTML-Code ohne Editor ein; Zeilenumbrüche setzt du mit br oder p. Die Zertifikatvorlage bestimmt Schrift, Größe, Farbe und Zeilenhöhe; die Textausrichtung kannst du für den Kurs überschreiben. Änderungen am Modus gelten für neue Ausstellungen. Neu im HTML-Modus gespeicherte Werte unterstützen zusätzlich small für kleineren Zusatztext; dessen Prozentgröße stammt aus dem Zertifikatselement. Bisherige HTML-Werte behalten ihre Interpretation bis zum erneuten Speichern im HTML-Modus. Bestehende Ausstellungen bleiben unverändert.';
$string['htmlvalue_help'] = 'Gib HTML-Code ein. Erlaubt sind strong/b (fett), em/i (kursiv), u (unterstrichen), ul/ol mit li (Listen), br (Zeilenumbruch) und p (Absatz). Verwende br oder p für Umbrüche; Enter im Quelltext erzeugt keinen festen Umbruch. Attribute und CSS werden entfernt. Schrift und Zeilenhöhe stammen aus der Zertifikatvorlage. Neu im HTML-Modus gespeicherte Werte unterstützen zusätzlich small für kleineren Zusatztext; dessen Prozentgröße stammt aus dem Zertifikatselement. Bisherige HTML-Werte behalten ihre Interpretation bis zum erneuten Speichern im HTML-Modus. Bestehende Ausstellungen bleiben unverändert.';
$string['adminhelp_htmlmode_article'] = '<p>Den Textmodus wählst du pro Feld in den Kurseinstellungen. Dieselbe Zertifikatvorlage kann Kurse mit Klartext und begrenztem HTML verwenden. Erlaubt sind strong/b, em/i, u, ul/ol mit li, br und p. Attribute wie style oder class, Schriftdefinitionen, Bilder und Skripte werden nicht übernommen. Nicht unterstützte Tags werden entfernt; ausführbare und eingebettete Inhalte werden verworfen.</p><p>Beispiel: <code>&lt;p&gt;&lt;strong&gt;Inhalte&lt;/strong&gt;&lt;/p&gt;&lt;ul&gt;&lt;li&gt;Thema A&lt;/li&gt;&lt;li&gt;Thema B&lt;/li&gt;&lt;/ul&gt;</code>. Absätze können im PDF zusätzlichen Abstand erzeugen. Verwende br für unmittelbar aufeinanderfolgende Zeilen. Die Vorlage bestimmt die Zeilenhöhe. Für kontrollierbare Listen ist linksbündige Ausrichtung meist passend.</p><p>Das Umschalten verändert keine gespeicherte Eingabe. Neue Ausstellungen übernehmen den gewählten Modus mit dem Text. Bestehende Ausstellungen behalten ihren gespeicherten Modus.</p><p>Kleinere Zusatzzeile: &lt;small&gt;Advanced Training&lt;/small&gt;&lt;br&gt;Certificate. Stelle die Reihenfolge um, um den Zusatz unter die Hauptzeile zu setzen. Die Zusatztext-Größe (%) im Zertifikatselement bestimmt die Größe. Verwende Begrenztes HTML und speichere den Kurswert erneut; bestehende Werte und Ausstellungen werden beim Upgrade nicht umgedeutet.</p>';
$string['invalidmode'] = 'Wähle Klartext oder begrenztes HTML.';
$string['invalidalignment'] = 'Wähle eine der angebotenen Textausrichtungen.';
$string['invalidanchor'] = 'Wähle Übernehmen, Oben, Mitte oder Unten.';
