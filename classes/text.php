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

/** Plain and limited-HTML export codec; interpretation travels with the saved value. */
final class text {
    private const HTML_PREFIX = '<div data-customfield-multiline-format="html">';
    private const SMALL_PREFIX = '<div data-customfield-multiline-format="html-small">';
    private const ENVELOPE = '/\A<div data-customfield-multiline-format="(html|html-small|plain)" ' .
        'data-customfield-multiline-align="([LCR]?)"(?: data-customfield-multiline-anchor="(top|middle|bottom)")?>(.*)<\/div>\z/s';
    public static function normalise(string $value): string {
        return str_replace(["\r\n", "\r", "\0"], ["\n", "\n", ''], $value);
    }

    public static function to_html(string $value): string {
        return implode('<br>', array_map('s', explode("\n", self::normalise($value))));
    }

    /** Include the selected semantic mode in the issue-owned export, never in live lookups. */
    public static function to_export(string $value, bool $htmlmode = false, string $alignment = '', string $anchor = '',
            bool $small = false): string {
        $small = $htmlmode && $small;
        $content = $htmlmode ? limited_html::clean(self::normalise($value), $small) : self::to_html($value);
        if ($anchor !== '' && !in_array($anchor, ['top', 'middle', 'bottom'], true)) {
            throw new \invalid_parameter_exception('Invalid multiline vertical anchor');
        }
        if (in_array($alignment, ['L', 'C', 'R'], true) || $anchor !== '') {
            $alignment = in_array($alignment, ['L', 'C', 'R'], true) ? $alignment : '';
            return '<div data-customfield-multiline-format="' . ($htmlmode ? ($small ? 'html-small' : 'html') : 'plain') .
                '" data-customfield-multiline-align="' . $alignment . '"' .
                ($anchor === '' ? '' : ' data-customfield-multiline-anchor="' . $anchor . '"') . '>' . $content . '</div>';
        }
        return $htmlmode ? ($small ? self::SMALL_PREFIX : self::HTML_PREFIX) . $content . '</div>' : $content;
    }

    /** Render old plain exports and mode-bearing HTML exports without decoding HTML entities twice. */
    public static function export_to_html(string $value): string {
        if (preg_match(self::ENVELOPE, $value, $matches)) {
            return $matches[1] !== 'plain' ? self::compact_html($matches[4], $matches[1] === 'html-small') :
                self::to_html(self::from_export($matches[4]));
        }
        if (str_starts_with($value, self::SMALL_PREFIX) && str_ends_with($value, '</div>')) {
            return self::compact_html(substr($value, strlen(self::SMALL_PREFIX), -6), true);
        }
        if (str_starts_with($value, self::HTML_PREFIX) && str_ends_with($value, '</div>')) {
            return self::compact_html(substr($value, strlen(self::HTML_PREFIX), -6));
        }
        return self::to_html(self::from_export($value));
    }

    /** Read only our exact generated envelope; raw user HTML cannot set presentation. */
    public static function export_alignment(string $value): string {
        return preg_match(self::ENVELOPE, $value, $matches) ? $matches[2] : '';
    }

    public static function export_vertical_anchor(string $value): string {
        return preg_match(self::ENVELOPE, $value, $matches) ? $matches[3] : '';
    }

    private static function compact_html(string $html, bool $small = false): string {
        // Keep native paragraph blocks; br avoids additional PDF paragraph spacing.
        return str_replace(['<p>', '</p>'], ['<div>', '</div>'], limited_html::clean($html, $small));
    }

    /** Decode our exported fragment once; callers must escape it again before rendering. */
    public static function from_export(string $value): string {
        return self::normalise(html_entity_decode(str_replace('<br>', "\n", $value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }
}
