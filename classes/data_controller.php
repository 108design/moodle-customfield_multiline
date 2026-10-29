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

/** Native customfield storage, permissions, backup and privacy remain in Core. */
class data_controller extends \core_customfield\data_controller {
    public function datafield(): string {
        return 'value';
    }

    public function instance_form_definition(\MoodleQuickForm $mform) {
        $name = $this->get_form_element_name();
        $mform->addElement('textarea', $name, $this->get_field()->get_formatted_name(), ['rows' => 4, 'cols' => 60]);
        $mform->setType($name, PARAM_RAW);
        $mform->addHelpButton($name, 'value', 'customfield_multiline');
        $mode = $mform->createElement('select', $name . '_format', get_string('htmlmode', 'customfield_multiline'), [
            FORMAT_PLAIN => get_string('plainmode', 'customfield_multiline'),
            FORMAT_HTML => get_string('limitedhtmlmode', 'customfield_multiline'),
        ]);
        $mode->setHiddenLabel(true);
        $info = $mform->createElement('static', $name . '_formatinfo', '', help::icon('htmlmode'));
        $mform->addGroup([$mode, $info], $name . '_formatgroup', get_string('htmlmode', 'customfield_multiline'), ' ', false);
        $mform->setType($name . '_format', PARAM_RAW);
        $mform->addElement('select', $name . '_alignment', get_string('alignment', 'customfield_multiline'), [
            '' => get_string('templatealignment', 'customfield_multiline'),
            'L' => get_string('alignleft', 'customfield_multiline'),
            'C' => get_string('aligncenter', 'customfield_multiline'), 'R' => get_string('alignright', 'customfield_multiline'),
        ]);
        $mform->setType($name . '_alignment', PARAM_RAW);
        $mform->addHelpButton($name . '_alignment', 'alignment', 'customfield_multiline');
        $mform->addElement('select', $name . '_verticalanchor', get_string('verticalanchor', 'customfield_multiline'), [
            '' => get_string('templateanchor', 'customfield_multiline'),
            'top' => get_string('anchortop', 'customfield_multiline'),
            'middle' => get_string('anchormiddle', 'customfield_multiline'),
            'bottom' => get_string('anchorbottom', 'customfield_multiline'),
        ]);
        $mform->setType($name . '_verticalanchor', PARAM_RAW);
        $mform->addHelpButton($name . '_verticalanchor', 'verticalanchor', 'customfield_multiline');
        if ($this->get_field()->get_configdata_property('required')) {
            $mform->addRule($name, null, 'required', null, 'client');
        }
    }

    /** Native backup/restore carries valueformat together with each course value. */
    public function get_format(): int {
        if ($this->get('id')) {
            return value_format::mode((int)$this->get('valueformat'));
        }
        // Preserve the default interpretation of fields created before per-course modes.
        return $this->get_field()->get_configdata_property('htmlmode') ? (int)FORMAT_HTML : (int)FORMAT_PLAIN;
    }

    public function get_alignment(): string {
        return $this->get('id') ? value_format::alignment((int)$this->get('valueformat')) : '';
    }

    public function get_vertical_anchor(): string {
        return $this->get('id') ? value_format::vertical_anchor((int)$this->get('valueformat')) : '';
    }

    public function instance_form_before_set_data(\stdClass $instance) {
        parent::instance_form_before_set_data($instance);
        $name = $this->get_form_element_name() . '_format';
        $instance->$name = $this->get_format();
        $instance->{$this->get_form_element_name() . '_alignment'} = $this->get_alignment();
        $instance->{$this->get_form_element_name() . '_verticalanchor'} = $this->get_vertical_anchor();
    }

    private function submitted_vertical_anchor(array $data): ?string {
        $name = $this->get_form_element_name() . '_verticalanchor';
        if (!array_key_exists($name, $data)) {
            return $this->get_vertical_anchor();
        }
        return in_array($data[$name], ['', 'top', 'middle', 'bottom'], true) ? $data[$name] : null;
    }

    private function submitted_alignment(array $data): ?string {
        $name = $this->get_form_element_name() . '_alignment';
        if (!array_key_exists($name, $data)) {
            return $this->get_alignment();
        }
        return in_array($data[$name], ['', 'L', 'C', 'R'], true) ? $data[$name] : null;
    }

    private function submitted_format(array $data): ?int {
        $name = $this->get_form_element_name() . '_format';
        if (!array_key_exists($name, $data)) {
            return $this->get_format();
        }
        foreach ([FORMAT_PLAIN, FORMAT_HTML] as $format) {
            if ($data[$name] === (int)$format || $data[$name] === (string)$format) {
                return (int)$format;
            }
        }
        return null;
    }

    public function instance_form_save(\stdClass $datanew) {
        $name = $this->get_form_element_name();
        if (!property_exists($datanew, $name)) {
            return;
        }
        $copy = clone $datanew;
        $copy->$name = text::normalise((string)$copy->$name);
        $format = $this->submitted_format((array)$datanew);
        $alignment = $this->submitted_alignment((array)$datanew);
        $anchor = $this->submitted_vertical_anchor((array)$datanew);
        if ($format === null || $alignment === null || $anchor === null) {
            throw new \invalid_parameter_exception('Invalid multiline text mode');
        }
        // A native form explicitly submits the mode. Partial updates retain historical interpretation.
        $small = $format === (int)FORMAT_HTML && (property_exists($datanew, $name . '_format') ||
            value_format::allows_small((int)$this->get('valueformat')));
        $this->set('valueformat', value_format::encode($format, $alignment, $anchor, $small));
        $this->set('valuetrust', false);
        parent::instance_form_save($copy);
    }

    public function instance_form_validation(array $data, array $files): array {
        $name = $this->get_form_element_name();
        $data[$name] = text::normalise((string)($data[$name] ?? ''));
        $errors = parent::instance_form_validation($data, $files);
        $format = $this->submitted_format($data);
        if ($format === null) {
            $errors[$name . '_format'] = get_string('invalidmode', 'customfield_multiline');
        }
        if ($this->submitted_alignment($data) === null) {
            $errors[$name . '_alignment'] = get_string('invalidalignment', 'customfield_multiline');
        }
        if ($this->submitted_vertical_anchor($data) === null) {
            $errors[$name . '_verticalanchor'] = get_string('invalidanchor', 'customfield_multiline');
        }
        $empty = $format === (int)FORMAT_HTML ?
            html_is_blank(limited_html::clean($data[$name], true)) : trim($data[$name]) === '';
        if ($this->get_field()->get_configdata_property('required') && $empty) {
            $errors[$name] = get_string('required');
        }
        return $errors;
    }

    public function get_default_value() {
        return text::normalise((string)($this->get_field()->get_configdata_property('defaultvalue') ?? ''));
    }

    public function export_value() {
        $value = (string)$this->get_value();
        return $value === '' ? null : text::to_export($value, $this->get_format() === (int)FORMAT_HTML,
            $this->get_alignment(), $this->get_vertical_anchor(), value_format::allows_small((int)$this->get('valueformat')));
    }
}
