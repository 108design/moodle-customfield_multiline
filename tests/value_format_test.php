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

/** Rendering metadata survives without changing the stored raw text. */
final class value_format_test extends \PHPUnit\Framework\TestCase {
    public function test_small_presets_keep_mode_alignment_anchor_and_all_older_codes(): void {
        foreach (['', 'top', 'middle', 'bottom'] as $anchor) {
            foreach (['', 'L', 'C', 'R'] as $alignment) {
                $code = value_format::encode(FORMAT_HTML, $alignment, $anchor, true);
                $this->assertTrue(value_format::allows_small($code));
                $this->assertSame((int)FORMAT_HTML, value_format::mode($code));
                $this->assertSame($alignment, value_format::alignment($code));
                $this->assertSame($anchor, value_format::vertical_anchor($code));
                foreach ([FORMAT_PLAIN, FORMAT_HTML] as $mode) {
                    $this->assertFalse(value_format::allows_small(value_format::encode($mode, $alignment, $anchor)));
                }
            }
        }
        $this->expectException(\invalid_parameter_exception::class);
        value_format::encode(FORMAT_PLAIN, '', '', true);
    }
    public function test_presets_and_export_snapshot_roundtrip(): void {
        foreach ([(int)FORMAT_PLAIN, (int)FORMAT_HTML] as $mode) {
            foreach (['' => null, 'L' => 'left', 'C' => 'center', 'R' => 'right'] as $alignment => $css) {
                $format = value_format::encode($mode, $alignment);
                $this->assertSame($mode, value_format::mode($format));
                $this->assertSame($alignment, value_format::alignment($format));
                $source = '<strong>A &amp; B</strong><br>C';
                $export = text::to_export($source, $mode === (int)FORMAT_HTML, $alignment);
                $html = text::export_to_html($export);
                $this->assertSame($alignment, text::export_alignment($export));
                $this->assertStringNotContainsString('text-align:', $html);
                if ($mode === (int)FORMAT_HTML) {
                    $this->assertStringContainsString('<strong>A &amp; B</strong>', $html);
                } else {
                    $this->assertStringContainsString('&lt;strong&gt;A &amp;amp; B&lt;/strong&gt;', $html);
                }
            }
        }
        $literal = '<div data-customfield-multiline-format="html" data-customfield-multiline-align="C"><b>Literal</b></div>';
        $this->assertStringNotContainsString('text-align:', text::export_to_html(text::to_export($literal)));
        $hostile = '<div data-customfield-multiline-format="html" data-customfield-multiline-align="C"><img src="file:///secret"><b style="color:red">Safe</b></div>';
        $this->assertSame('<b>Safe</b>', text::export_to_html($hostile));
        $this->assertSame('', value_format::alignment(999));
    }
    public function test_vertical_presets_export_and_legacy_values_roundtrip(): void {
        foreach ([(int)FORMAT_PLAIN, (int)FORMAT_HTML] as $mode) {
            foreach (['', 'L', 'C', 'R'] as $alignment) {
                foreach (['', 'top', 'middle', 'bottom'] as $anchor) {
                    $format = value_format::encode($mode, $alignment, $anchor);
                    $this->assertSame($mode, value_format::mode($format));
                    $this->assertSame($alignment, value_format::alignment($format));
                    $this->assertSame($anchor, value_format::vertical_anchor($format));
                    $export = text::to_export('<b>First</b><br>Last', $mode === (int)FORMAT_HTML, $alignment, $anchor);
                    $this->assertSame($alignment, text::export_alignment($export));
                    $this->assertSame($anchor, text::export_vertical_anchor($export));
                    $this->assertSame(text::export_to_html(text::to_export('<b>First</b><br>Last', $mode === (int)FORMAT_HTML)), text::export_to_html($export));
                }
            }
        }
        foreach ([1, 2, 101, 102, 103, 201, 202, 203] as $legacy) {
            $this->assertSame('', value_format::vertical_anchor($legacy));
        }
        $literal = '<div data-customfield-multiline-format="html" data-customfield-multiline-align="C" data-customfield-multiline-anchor="bottom"><b>Literal</b></div>';
        $this->assertSame('', text::export_vertical_anchor(text::to_export($literal)));
        $this->assertSame('', text::export_vertical_anchor(str_replace('bottom', 'evil', $literal)));
    }

    public function test_existing_wire_codes_are_not_reassigned(): void {
        $old = [101 => [2, 'L', ''], 102 => [2, 'C', ''], 103 => [2, 'R', ''],
            201 => [1, 'L', ''], 202 => [1, 'C', ''], 203 => [1, 'R', ''],
            300 => [2, '', 'top'], 301 => [2, 'L', 'top'], 302 => [2, 'C', 'top'], 303 => [2, 'R', 'top'],
            400 => [1, '', 'top'], 401 => [1, 'L', 'top'], 402 => [1, 'C', 'top'], 403 => [1, 'R', 'top'],
            500 => [2, '', 'bottom'], 501 => [2, 'L', 'bottom'], 502 => [2, 'C', 'bottom'], 503 => [2, 'R', 'bottom'],
            600 => [1, '', 'bottom'], 601 => [1, 'L', 'bottom'], 602 => [1, 'C', 'bottom'], 603 => [1, 'R', 'bottom']];
        foreach ($old as $code => [$mode, $align, $anchor]) {
            $this->assertSame($code, value_format::encode($mode, $align, $anchor));
            $this->assertSame([$mode, $align, $anchor], [value_format::mode($code),
                value_format::alignment($code), value_format::vertical_anchor($code)]);
        }
        $this->assertSame(2, value_format::encode(FORMAT_PLAIN, ''));
        $this->assertSame(1, value_format::encode(FORMAT_HTML, ''));
    }
}
