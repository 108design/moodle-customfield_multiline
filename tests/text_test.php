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
namespace customfield_multiline;
defined('MOODLE_INTERNAL') || die();

/** Stateless boundaries do not need a database reset. */
final class text_test extends \PHPUnit\Framework\TestCase {
    public function test_small_is_versioned_strips_attributes_flattens_nesting_and_keeps_legacy_exports(): void {
        $raw = '<small style="font-size:999pt" onclick="bad()">A <small>B</small></small><br>Certificate';
        $this->assertSame('A B<br>Certificate', limited_html::clean($raw));
        $this->assertSame('<small>A B</small><br>Certificate', limited_html::clean($raw, true));
        foreach (['', 'top', 'middle', 'bottom'] as $anchor) {
            foreach (['', 'L', 'C', 'R'] as $alignment) {
                $new = text::to_export($raw, true, $alignment, $anchor, true);
                $this->assertSame('<small>A B</small><br>Certificate', text::export_to_html($new));
                $this->assertSame($alignment, text::export_alignment($new));
                $this->assertSame($anchor, text::export_vertical_anchor($new));
                $legacy = text::to_export($raw, true, $alignment, $anchor);
                $this->assertSame('A B<br>Certificate', text::export_to_html($legacy));
                $this->assertSame($new, text::to_export(text::export_to_html($new), true, $alignment, $anchor, true));
            }
        }
        $this->assertSame('&lt;small&gt;Literal&lt;/small&gt;',
            text::export_to_html(text::to_export('&lt;small&gt;Literal&lt;/small&gt;', true, '', '', true)));
        $this->assertSame(text::to_html($raw), text::export_to_html(text::to_export($raw)));
        $this->assertSame('A B<br>Certificate', text::export_to_html(
            '<div data-customfield-multiline-format="html">' . $raw . '</div>'));
    }
    public function test_literal_roundtrip_retains_breaks_empty_lines_and_entities(): void {
        $text = "Erste & <b>keine Formatierung</b>\r\n\r\nGröße > 0\rLetzte";
        $expected = "Erste & <b>keine Formatierung</b>\n\nGröße > 0\nLetzte";
        $html = text::to_html($text);
        $this->assertStringNotContainsString('<b>', $html);
        $this->assertSame(3, substr_count($html, '<br>'));
        $this->assertSame($expected, text::from_export($html));
        $this->assertSame('&lt;b&gt;', text::from_export(text::to_html('&lt;b&gt;')));
        $this->assertSame("0\nEnd", text::normalise("0\nEnd\0"));
    }
    public function test_limited_html_preserves_only_semantic_tags_and_text(): void {
        $raw = '<p style="text-align:right"><strong class="x">Größe &amp; Qualität</strong><br><em>kursiv</em><u>unterstrichen</u></p>' .
            '<ul><li onclick="alert(1)">A</li><li>B</li></ul><ol start="5"><li>C</li></ol>';
        $clean = limited_html::clean($raw);
        $this->assertSame('<p><strong>Größe &amp; Qualität</strong><br><em>kursiv</em><u>unterstrichen</u></p>' .
            '<ul><li>A</li><li>B</li></ul><ol><li>C</li></ol>', $clean);
        $this->assertSame($clean, limited_html::clean($clean));
    }

    public function test_executable_attributes_resources_and_entity_encoded_markup_stay_inert(): void {
        $raw = '<script>evil()</script><style>bad{}</style><iframe src="https://example.invalid">frame</iframe>' .
            '<img src="file:///secret"><svg><script>x</script></svg><object>obj</object>' .
            '<a href="javascript:alert(1)">Link text</a><span style="font-size:80pt">Small</span>' .
            '&lt;b&gt;literal&lt;/b&gt;<!--comment--><b onmouseover="x">Bold</b>';
        $this->assertSame('Link textSmall&lt;b&gt;literal&lt;/b&gt;<b>Bold</b>', limited_html::clean($raw));
        $this->assertSame('<strong>Open</strong>', limited_html::clean('<strong>Open'));
        $this->assertSame('<p>Orphan</p>', limited_html::clean('<li>Orphan</li>'));
    }

    public function test_export_carries_html_mode_without_reinterpreting_plain_text(): void {
        $raw = '<p><strong>Title</strong></p><ul><li>A &amp; B</li></ul>';
        $export = text::to_export($raw, true);
        $this->assertSame('<div><strong>Title</strong></div><ul><li>A &amp; B</li></ul>', text::export_to_html($export));
        $this->assertSame(text::to_html($raw), text::export_to_html(text::to_export($raw)));
        $this->assertSame('Old<br><br>&lt;b&gt;literal&lt;/b&gt;', text::export_to_html('Old<br><br>&lt;b&gt;literal&lt;/b&gt;'));
        $forged = '<div data-customfield-multiline-format="html"><script>bad</script><b>ok</b></div>';
        $this->assertSame(text::to_html($forged), text::export_to_html(text::to_export($forged)));
        $this->assertSame('<b>ok</b>', text::export_to_html($forged));
    }

}
