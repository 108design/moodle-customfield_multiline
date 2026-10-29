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

/** Plugin rendering presets carried by native valueformat, including in backups. */
final class value_format {
    // New HTML semantics are opt-in on save; legacy presets never acquire new tags on upgrade.
    private const SMALL_PRESETS = [900 => ['', ''], 901 => ['L', ''], 902 => ['C', ''], 903 => ['R', ''],
        910 => ['', 'top'], 911 => ['L', 'top'], 912 => ['C', 'top'], 913 => ['R', 'top'],
        920 => ['', 'middle'], 921 => ['L', 'middle'], 922 => ['C', 'middle'], 923 => ['R', 'middle'],
        930 => ['', 'bottom'], 931 => ['L', 'bottom'], 932 => ['C', 'bottom'], 933 => ['R', 'bottom']];
    // Native PLAIN/HTML retain template alignment; plugin presets add L/C/R.
    private const PRESETS = [101 => [2, 'L', ''], 102 => [2, 'C', ''], 103 => [2, 'R', ''],
        201 => [1, 'L', ''], 202 => [1, 'C', ''], 203 => [1, 'R', ''],
        300 => [2, '', 'top'], 301 => [2, 'L', 'top'], 302 => [2, 'C', 'top'], 303 => [2, 'R', 'top'],
        400 => [1, '', 'top'], 401 => [1, 'L', 'top'], 402 => [1, 'C', 'top'], 403 => [1, 'R', 'top'],
        500 => [2, '', 'bottom'], 501 => [2, 'L', 'bottom'], 502 => [2, 'C', 'bottom'], 503 => [2, 'R', 'bottom'],
        600 => [1, '', 'bottom'], 601 => [1, 'L', 'bottom'], 602 => [1, 'C', 'bottom'], 603 => [1, 'R', 'bottom'],
        700 => [2, '', 'middle'], 701 => [2, 'L', 'middle'], 702 => [2, 'C', 'middle'], 703 => [2, 'R', 'middle'],
        800 => [1, '', 'middle'], 801 => [1, 'L', 'middle'], 802 => [1, 'C', 'middle'], 803 => [1, 'R', 'middle']];

    public static function mode(int $format): int {
        if (self::allows_small($format)) {
            return (int)FORMAT_HTML;
        }
        return self::PRESETS[$format][0] ?? ($format === (int)FORMAT_HTML ? (int)FORMAT_HTML : (int)FORMAT_PLAIN);
    }

    public static function alignment(int $format): string {
        return self::SMALL_PRESETS[$format][0] ?? self::PRESETS[$format][1] ?? '';
    }

    public static function vertical_anchor(int $format): string {
        return self::SMALL_PRESETS[$format][1] ?? self::PRESETS[$format][2] ?? '';
    }

    public static function allows_small(int $format): bool {
        return isset(self::SMALL_PRESETS[$format]);
    }

    public static function encode(int $mode, string $alignment, string $anchor = '', bool $small = false): int {
        if ($small) {
            if ($mode === (int)FORMAT_HTML) {
                foreach (self::SMALL_PRESETS as $format => $preset) {
                    if ($preset === [$alignment, $anchor]) {
                        return $format;
                    }
                }
            }
            throw new \invalid_parameter_exception('Invalid multiline small-text preset');
        }
        foreach (self::PRESETS as $format => $preset) {
            if ($preset === [$mode, $alignment, $anchor]) {
                return $format;
            }
        }
        if ($anchor !== '' || $alignment !== '' || !in_array($mode, [(int)FORMAT_HTML, (int)FORMAT_PLAIN], true)) {
            throw new \invalid_parameter_exception('Invalid multiline rendering preset');
        }
        return $mode;
    }
}
