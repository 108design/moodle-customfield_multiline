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
$string['pluginname'] = 'Certificate Multitool – Custom Field';
$string['specificsettings'] = 'Certificate Multitool – Custom Field';
$string['defaultvalue'] = 'Default text';
$string['defaultvalue_help'] = 'Enter plain text. Enter inserts a hard line break; an empty line remains empty. The default is used only while no course value has been saved. A saved empty value stays empty. Set certificate typography and alignment in the certificate template.';
$string['value'] = 'Multiline text';
$string['value_help'] = 'Choose the text mode for this course below. Plain text uses Enter for hard breaks and displays HTML tags literally. Limited HTML uses br or p for breaks. Font, size, colour and width are set in the certificate template. Alignment can be overridden below for this course.';
$string['privacy:metadata'] = 'Field content is stored and processed by the Moodle custom fields subsystem.';
$string['adminhelp'] = 'Help: Certificate Multitool – Custom Field';
$string['adminhelptopics'] = 'Help Topics';
$string['adminhelpmore'] = 'More in the help panel';
$string['overview'] = 'Plain text and line breaks';
$string['overview_help'] = 'Use Enter for a hard line break. Presentation is set in the certificate template.';
$string['issuance'] = 'Certificates and saved text';
$string['issuance_help'] = 'Issued certificates retain their saved text. More detail explains defaults and previews.';
$string['adminhelp_overview'] = 'Entering multiline text';
$string['adminhelp_overview_article'] = '<p>Choose Certificate Multitool – Custom Field when creating a course custom field. Enter one or more lines in course settings. Enter inserts a hard line break; an empty line adds spacing. In Plain text mode, HTML tags are treated literally. Select Limited HTML under Text mode to enable the supported tags.</p><p>Font, width and position come from the certificate template. Override horizontal alignment and vertical anchor for each course, or inherit the element defaults. With an Area height, Top or Bottom aligns text to the respective area edge. Without a height, Top places the upper text edge and Bottom the lower text edge at Position Y. Middle vertically centres the entire text block within the fixed area. It requires an Area height in the certificate element.</p>';
$string['adminhelp_issuance'] = 'Defaults and certificates';
$string['adminhelp_issuance_article'] = '<p>The default applies until a course value is saved. Clearing and saving the field deliberately suppresses the default.</p><p>New certificates receive the current course value through Course certificate. Existing issues retain their saved text, including when their PDF is regenerated. The template still controls layout. Changing a template to select another field does not add missing historical values.</p>';

$string['htmlmode'] = 'Text mode';
$string['plainmode'] = 'Plain text';
$string['limitedhtmlmode'] = 'Limited HTML';
$string['htmlmode_help'] = 'Choose the mode for this course and field. New fields start with Plain text; existing values retain their previous mode on upgrade. Plain text: Enter inserts hard line breaks and HTML is displayed literally. Limited HTML recognises only strong, b, em, i, u, ul, ol, li, br and p. Attributes, CSS, images and other embeds are not retained. Enter HTML source without an editor; use br or p for breaks. The certificate template controls font, size, colour and line height; text alignment can be overridden for the course. Mode changes apply to new issues. Values newly saved in HTML mode also support small for smaller extra text; its percentage size comes from the certificate element. Previously saved HTML retains its interpretation until saved again in HTML mode. Existing issues retain their interpretation.';
$string['htmlvalue'] = 'Enter limited HTML';
$string['htmlvalue_help'] = 'Enter HTML source. Allowed tags are strong/b (bold), em/i (italic), u (underline), ul/ol with li (lists), br (line break) and p (paragraph). Use br or p for breaks; Enter in the source does not create a hard break. Attributes and CSS are removed. Font and line height come from the certificate template. Values newly saved in HTML mode also support small for smaller extra text; its percentage size comes from the certificate element. Previously saved HTML retains its interpretation until saved again in HTML mode. Existing issues retain their interpretation.';

$string['adminhelp_htmlmode'] = 'Limited HTML';
$string['adminhelp_htmlmode_article'] = '<p>Choose the text mode for each field in course settings. The same certificate template can use courses with Plain text and Limited HTML. Allowed tags are strong/b, em/i, u, ul/ol with li, br and p. Attributes such as style or class, font definitions, images and scripts are not retained. Unsupported tags are removed; executable and embedded content is discarded.</p><p>Example: <code>&lt;p&gt;&lt;strong&gt;Topics&lt;/strong&gt;&lt;/p&gt;&lt;ul&gt;&lt;li&gt;Topic A&lt;/li&gt;&lt;li&gt;Topic B&lt;/li&gt;&lt;/ul&gt;</code>. Paragraphs can add spacing in the PDF. Use br for consecutive lines without paragraph spacing. The template controls line height. Left alignment usually suits lists.</p><p>Switching modes does not change stored source. New issues save the selected mode with the text. Existing issues retain their saved mode.</p><p>Smaller extra line: &lt;small&gt;Advanced Training&lt;/small&gt;&lt;br&gt;Certificate. Reverse the order to put the extra text below the main line. Small text size (%) in the certificate element controls the size. Choose Limited HTML and save the course value again; upgrading does not reinterpret existing values or issues.</p>';

$string['invalidmode'] = 'Choose Plain text or Limited HTML.';

$string['alignment'] = 'Text alignment';
$string['templatealignment'] = 'Use template alignment';
$string['alignment_help'] = 'Applies to this field in this course, for Plain text and HTML. Use template alignment retains the previous alignment. Left, centre or right overrides text alignment within the block positioned by the template. Width, position, font and line height remain in the template. Lists retain their indents and left-aligned items. New issues save this choice; existing issues retain their saved presentation.';
$string['invalidalignment'] = 'Choose one of the available text alignments.';
$string['alignleft'] = 'Left';
$string['aligncenter'] = 'Centre';
$string['alignright'] = 'Right';

$string['verticalanchor'] = 'Vertical alignment';
$string['anchortop'] = 'Top';
$string['anchormiddle'] = 'Middle';
$string['anchorbottom'] = 'Bottom';
$string['verticalanchor_help'] = 'Inherit from template uses the certificate element default. With an area height, Top aligns text to the upper edge and Bottom to the lower edge of the area. Position and area height are configured only in the template. Without an area height, legacy anchoring applies: Top places the upper text edge at Position Y and Bottom the lower text edge. This selection applies to this field in this course and is saved with new issues. Existing issues retain their saved selection. Middle vertically centres the entire text block within the fixed area. It requires an Area height in the certificate element.';
$string['invalidanchor'] = 'Choose Inherit, Top, Middle or Bottom.';
$string['templateanchor'] = 'Inherit from template';
