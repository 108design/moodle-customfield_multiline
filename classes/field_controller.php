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

/** Defines editor-free multiline course content. */
class field_controller extends \core_customfield\field_controller {
    public const TYPE = 'multiline';

    public function config_form_definition(\MoodleQuickForm $mform) {
        $mform->addElement('header', 'multiline_settings', get_string('specificsettings', 'customfield_multiline'));
        $mform->addElement('textarea', 'configdata[defaultvalue]', get_string('defaultvalue', 'core_customfield'),
            ['rows' => 4, 'cols' => 60]);
        $mform->setType('configdata[defaultvalue]', PARAM_RAW);
        $mform->addHelpButton('configdata[defaultvalue]', 'defaultvalue', 'customfield_multiline');
    }

    public function save() {
        global $DB;
        $config = $this->get('configdata');
        // Core replaces configdata on form save. Retain the legacy default internally,
        // including for courses which have not yet saved a value.
        if ($this->get('id')) {
            $old = json_decode($DB->get_field('customfield_field', 'configdata', ['id' => $this->get('id')]), true);
            $config['htmlmode'] = (int)(($old['htmlmode'] ?? 0) == 1);
        } else {
            $config['htmlmode'] = (int)(($config['htmlmode'] ?? 0) == 1);
        }
        $config['defaultvalue'] = text::normalise((string)($config['defaultvalue'] ?? ''));
        $this->set('configdata', json_encode($config));
        parent::save();
    }
}
