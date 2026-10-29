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

/** Upgrade operations; never reinterpret stored values during normal reads. */
final class upgrade {
    /** Freeze the pre-0.3 field-wide mode in each existing value. Run once only. */
    public static function freeze_legacy_modes(): void {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $fields = $DB->get_recordset('customfield_field', ['type' => 'multiline'], '', 'id, configdata');
        try {
            foreach ($fields as $field) {
                $config = json_decode($field->configdata, true);
                $format = ($config['htmlmode'] ?? 0) == 1 ? FORMAT_HTML : FORMAT_PLAIN;
                $DB->set_field('customfield_data', 'valueformat', $format, ['fieldid' => $field->id]);
            }
        } finally {
            $fields->close();
        }
        $transaction->allow_commit();
    }
}
