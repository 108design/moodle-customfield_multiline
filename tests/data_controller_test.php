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

/** Native field lifecycle and course permissions. */
final class data_controller_test extends \advanced_testcase {
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    private function field(array $config = []): \core_customfield\field_controller {
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $category = $generator->create_category();
        return $generator->create_field(['type' => 'multiline', 'categoryid' => $category->get('id'),
            'configdata' => $config]);
    }

    public function test_defaults_save_reopen_explicit_empty_and_long_literal_text(): void {
        $field = $this->field(['defaultvalue' => "Default\r\nSecond"]);
        $course = $this->getDataGenerator()->create_course();
        $value = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $field);
        $this->assertSame("Default\nSecond", $value->get_value());
        $literal = str_repeat('Ö <b> & ', 300) . "\n\nLast";
        $name = $value->get_form_element_name();
        $value->set('contextid', $value->get_context()->id);
        $value->instance_form_save((object)[$name => $literal]);
        $reopened = \core_customfield\data_controller::create($value->get('id'));
        $this->assertSame($literal, $reopened->get_value());
        $this->assertSame((int)FORMAT_PLAIN, (int)$reopened->get('valueformat'));
        $this->assertStringNotContainsString('<b>', $reopened->export_value());
        $reopened->instance_form_save((object)[$name => '']);
        $this->assertSame('', $reopened->get_value());
        $this->assertNull($reopened->export_value());
        $field->delete();
        global $DB;
        $this->assertFalse($DB->record_exists('customfield_data', ['id' => $value->get('id')]));
    }

    public function test_required_unique_and_native_visibility_lock_boundaries(): void {
        $field = $this->field(['required' => 1, 'uniquevalues' => 1, 'visibility' => 0, 'locked' => 1]);
        $course = $this->getDataGenerator()->create_course();
        $value = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $field);
        $name = $value->get_form_element_name();
        $this->assertArrayHasKey($name, $value->instance_form_validation([$name => " \n"], []));
        $value->set('contextid', $value->get_context()->id);
        $value->instance_form_save((object)[$name => "A\nB"]);
        $other = \core_customfield\data_controller::create(0,
            (object)['instanceid' => $this->getDataGenerator()->create_course()->id], $field);
        $this->assertArrayHasKey($name, $other->instance_form_validation([$name => "A\nB"], []));
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->setUser($teacher);
        $this->assertFalse($field->get_handler()->can_view($field, $course->id));
        $this->assertFalse($field->get_handler()->can_edit($field, $course->id));
        $this->setAdminUser();
        $this->assertTrue($field->get_handler()->can_edit($field, $course->id));
    }

    public function test_native_course_backup_restore_keeps_literal_newlines(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
        $field = $this->field();
        $course = $this->getDataGenerator()->create_course();
        $value = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $field);
        $text = "One & <literal>\n\nDrei\nVier";
        $value->set('contextid', $value->get_context()->id);
        $value->instance_form_save((object)[$value->get_form_element_name() => $text]);
        $htmlfield = $this->field();
        $htmlvalue = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $htmlfield);
        $htmlvalue->set('contextid', $htmlvalue->get_context()->id);
        $htmlvalue->instance_form_save((object)[$htmlvalue->get_form_element_name() => '<p><small>Topics</small></p>',
            $htmlvalue->get_form_element_name() . '_format' => FORMAT_HTML,
            $htmlvalue->get_form_element_name() . '_alignment' => 'C',
            $htmlvalue->get_form_element_name() . '_verticalanchor' => 'bottom']);
        $middlefield = $this->field();
        $middlevalue = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $middlefield);
        $middlevalue->set('contextid', $middlevalue->get_context()->id);
        $middlevalue->instance_form_save((object)[$middlevalue->get_form_element_name() => "Middle\nTitle",
            $middlevalue->get_form_element_name() . '_verticalanchor' => 'middle']);
        $backup = new \backup_controller(\backup::TYPE_1COURSE, $course->id, \backup::FORMAT_MOODLE,
            \backup::INTERACTIVE_NO, \backup::MODE_GENERAL, 2);
        $backup->execute_plan();
        $backupid = $backup->get_backupid();
        $backup->destroy();
        $files = get_file_storage()->get_area_files(\context_course::instance($course->id)->id,
            'backup', 'course', false, 'id', false);
        get_file_packer('application/vnd.moodle.backup')->extract_to_pathname(reset($files),
            $CFG->tempdir . '/backup/' . $backupid);
        $restoredid = \restore_dbops::create_new_course('Restored multiline', 'restored-multiline', $course->category);
        $restore = new \restore_controller($backupid, $restoredid, \backup::INTERACTIVE_NO,
            \backup::MODE_GENERAL, 2, \backup::TARGET_NEW_COURSE);
        $this->assertTrue($restore->execute_precheck());
        $restore->execute_plan();
        $restore->destroy();
        $values = \core_course\customfield\course_handler::create()->get_instance_data($restoredid, true);
        $this->assertCount(3, $values);
        $byfield = [];
        foreach ($values as $restored) { $byfield[$restored->get_field()->get('id')] = $restored; }
        $this->assertSame($text, $byfield[$field->get('id')]->get_value());
        $this->assertSame((int)FORMAT_PLAIN, $byfield[$field->get('id')]->get_format());
        $this->assertSame('<p><small>Topics</small></p>', $byfield[$htmlfield->get('id')]->get_value());
        $this->assertTrue(value_format::allows_small((int)$byfield[$htmlfield->get('id')]->get('valueformat')));
        $this->assertSame('<div><small>Topics</small></div>', text::export_to_html($byfield[$htmlfield->get('id')]->export_value()));
        $this->assertSame((int)FORMAT_HTML, $byfield[$htmlfield->get('id')]->get_format());
        $this->assertSame('C', $byfield[$htmlfield->get('id')]->get_alignment());
        $this->assertSame('bottom', $byfield[$htmlfield->get('id')]->get_vertical_anchor());
        $this->assertSame('middle', $byfield[$middlefield->get('id')]->get_vertical_anchor());
        $this->assertSame("Middle\nTitle", $byfield[$middlefield->get('id')]->get_value());
    }
    public function test_course_modes_save_reopen_partial_save_and_validation(): void {
        $field = $this->field(['required' => 1]);
        $course = $this->getDataGenerator()->create_course();
        $othercourse = $this->getDataGenerator()->create_course();
        $html = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $field);
        $plain = \core_customfield\data_controller::create(0, (object)['instanceid' => $othercourse->id], $field);
        $name = $html->get_form_element_name();
        $raw = '<p style="color:red">A<br><b>B</b></p><img src="file:///secret">';
        foreach ([$html, $plain] as $value) {
            $value->set('contextid', $value->get_context()->id);
            $value->instance_form_save((object)[$name => $raw, $name . '_format' => $value === $html ? FORMAT_HTML : FORMAT_PLAIN,
                $name . '_alignment' => $value === $html ? 'C' : 'L']);
        }
        $saved = \core_customfield\data_controller::create($html->get('id'));
        $this->assertSame($raw, $saved->get_value());
        $this->assertSame('<div>A<br><b>B</b></div>', text::export_to_html($saved->export_value()));
        $this->assertSame('C', $saved->get_alignment());
        $this->assertSame(text::to_html($raw), text::export_to_html($plain->export_value()));
        $data = new \stdClass();
        $saved->instance_form_before_set_data($data);
        $this->assertEquals(FORMAT_HTML, $data->{$name . '_format'});
        $saved->instance_form_save((object)[$name => $raw]);
        $this->assertSame((int)FORMAT_HTML, $saved->get_format());
        $saved->instance_form_save((object)[$name . '_format' => FORMAT_PLAIN]);
        $this->assertSame((int)FORMAT_HTML, $saved->get_format());
        $this->assertArrayHasKey($name, $saved->instance_form_validation(
            [$name => '<script>bad</script>', $name . '_format' => FORMAT_HTML], []));
        $this->assertArrayNotHasKey($name, $saved->instance_form_validation(
            [$name => '<script>bad</script>', $name . '_format' => FORMAT_PLAIN], []));
        $this->assertArrayHasKey($name . '_alignment', $saved->instance_form_validation(
            [$name => $raw, $name . '_alignment' => '<style>'], []));
        foreach (['bad', 0, [], '1junk'] as $invalid) {
            $this->assertArrayHasKey($name . '_format', $saved->instance_form_validation(
                [$name => $raw, $name . '_format' => $invalid], []));
        }
        $saved->instance_form_save((object)[$name => $raw, $name . '_format' => (string)FORMAT_PLAIN, $name . '_alignment' => '']);
        $this->assertSame(text::to_html($raw), $saved->export_value());
        $this->assertSame($raw, $saved->get_value());
        $this->expectException(\invalid_parameter_exception::class);
        $saved->instance_form_save((object)[$name => 'Must not save', $name . '_format' => 'invalid']);
    }

    public function test_small_activation_requires_explicit_html_save_and_preserves_legacy_partial_updates(): void {
        $field = $this->field();
        $course = $this->getDataGenerator()->create_course();
        $value = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $field);
        $value->set('contextid', $value->get_context()->id);
        $raw = '<small>Advanced</small><br>Certificate';
        $value->set('value', $raw);
        $value->set('valueformat', value_format::encode(FORMAT_HTML, 'C', 'middle'));
        $value->save();
        $name = $value->get_form_element_name();
        $old = $value->export_value();
        $value->instance_form_save((object)[$name => $raw]);
        $this->assertSame($old, $value->export_value());
        $this->assertSame('Advanced<br>Certificate', text::export_to_html($old));
        $value->instance_form_save((object)[$name => $raw, $name . '_format' => FORMAT_HTML]);
        $reopened = \core_customfield\data_controller::create($value->get('id'));
        $this->assertSame($raw, $reopened->get_value());
        $this->assertSame('<small>Advanced</small><br>Certificate', text::export_to_html($reopened->export_value()));
        $this->assertSame('C', $reopened->get_alignment());
        $this->assertSame('middle', $reopened->get_vertical_anchor());
        $reopened->instance_form_save((object)[$name => $raw]);
        $this->assertTrue(value_format::allows_small((int)$reopened->get('valueformat')));
    }

    public function test_native_forms_show_per_course_mode_and_preserve_legacy_default(): void {
        global $PAGE;
        $PAGE->set_url('/course/customfield.php');
        $field = $this->field(['htmlmode' => 1, 'defaultvalue' => '<b>Default</b>']);
        $form = new \core_customfield\field_config_form(null, null, 'post', '', [], true, ['id' => $field->get('id')], true);
        $form->set_data_for_dynamic_submission();
        $html = $form->render();
        $this->assertStringNotContainsString('name="configdata[htmlmode]"', $html);
        $this->assertStringContainsString('name="configdata[defaultvalue]"', $html);
        $data = \core_customfield\api::prepare_field_for_config_form($field);
        unset($data->configdata['htmlmode']);
        $submitted = \core_customfield\field_config_form::mock_ajax_submit((array)$data);
        $saveform = new \core_customfield\field_config_form(null, null, 'post', '', [], true, $submitted, true);
        $this->assertNotFalse($saveform->get_data());
        $saveform->process_dynamic_submission();
        $reopened = \core_customfield\field_controller::create($field->get('id'));
        $this->assertSame(1, (int)$reopened->get_configdata_property('htmlmode'));
        $course = $this->getDataGenerator()->create_course();
        $value = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $reopened);
        $this->assertSame((int)FORMAT_HTML, $value->get_format());
        $this->assertStringContainsString('<b>Default</b>', $value->export_value());
        $newfield = $this->field();
        $newvalue = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $newfield);
        $this->assertSame((int)FORMAT_PLAIN, $newvalue->get_format());
        $courseform = new class(null, $value) extends \moodleform {
            public function definition() { $this->_customdata->instance_form_definition($this->_form); }
        };
        $defaults = new \stdClass();
        $value->instance_form_before_set_data($defaults);
        $courseform->set_data($defaults);
        $rendered = $courseform->render();
        $this->assertStringContainsString('name="' . $value->get_form_element_name() . '_format"', $rendered);
        $this->assertStringContainsString('multilinehelp=htmlmode', $rendered);
        $this->assertMatchesRegularExpression('/<option[^>]*value="1"[^>]*selected/', $rendered);
    }

    public function test_upgrade_freezes_existing_modes_without_touching_values_or_other_types(): void {
        global $DB;
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $htmlfield = $this->field(['htmlmode' => 1, 'visibility' => 0]);
        $plainfield = $this->field();
        $otherfield = $generator->create_field(['type' => 'textarea', 'categoryid' => $plainfield->get('categoryid')]);
        $records = [];
        foreach ([$htmlfield, $plainfield, $otherfield] as $field) {
            $value = \core_customfield\data_controller::create(0, (object)[
                'instanceid' => $this->getDataGenerator()->create_course()->id,
                'contextid' => \context_system::instance()->id], $field);
            $value->set('value', '<p>Old & raw</p>');
            $value->set('valueformat', FORMAT_PLAIN);
            $value->save();
            $records[$value->get('id')] = $DB->get_record('customfield_data', ['id' => $value->get('id')]);
        }
        upgrade::freeze_legacy_modes();
        foreach ($records as $id => $before) {
            $after = $DB->get_record('customfield_data', ['id' => $id]);
            $this->assertEquals($before->fieldid == $htmlfield->get('id') ? FORMAT_HTML : FORMAT_PLAIN, $after->valueformat);
            $after->valueformat = $before->valueformat;
            $this->assertEquals($before, $after);
        }
        $this->assertSame(0, (int)\core_customfield\field_controller::create($htmlfield->get('id'))->get_configdata_property('visibility'));
    }
    public function test_vertical_anchor_is_per_value_and_partial_saves_preserve_it(): void {
        $field = $this->field();
        $first = \core_customfield\data_controller::create(0,
            (object)['instanceid' => $this->getDataGenerator()->create_course()->id], $field);
        $second = \core_customfield\data_controller::create(0,
            (object)['instanceid' => $this->getDataGenerator()->create_course()->id], $field);
        $name = $first->get_form_element_name();
        $this->assertSame('', $first->get_vertical_anchor());
        foreach ([$first, $second] as $value) {
            $value->set('contextid', $value->get_context()->id);
            $value->instance_form_save((object)[$name => "One\nTwo", $name . '_verticalanchor' => $value === $first ? 'bottom' : 'top']);
        }
        $saved = \core_customfield\data_controller::create($first->get('id'));
        $this->assertSame('bottom', $saved->get_vertical_anchor());
        $this->assertSame('top', $second->get_vertical_anchor());
        $this->assertSame('bottom', text::export_vertical_anchor($saved->export_value()));
        $saved->instance_form_save((object)[$name => 'Changed']);
        $saved->instance_form_save((object)[$name . '_verticalanchor' => 'top']);
        $this->assertSame('bottom', $saved->get_vertical_anchor());
        $defaults = new \stdClass();
        $saved->instance_form_before_set_data($defaults);
        $this->assertSame('bottom', $defaults->{$name . '_verticalanchor'});
        $this->assertArrayHasKey($name . '_verticalanchor', $saved->instance_form_validation(
            [$name => 'Valid text', $name . '_verticalanchor' => []], []));
        $saved->instance_form_save((object)[$name => 'Changed', $name . '_verticalanchor' => '']);
        $this->assertSame('', $saved->get_vertical_anchor());
    }

    public function test_middle_saves_reopens_exports_and_survives_partial_save_for_both_modes(): void {
        $field = $this->field();
        foreach ([FORMAT_PLAIN, FORMAT_HTML] as $mode) {
            $course = $this->getDataGenerator()->create_course();
            $value = \core_customfield\data_controller::create(0, (object)['instanceid' => $course->id], $field);
            $value->set('contextid', $value->get_context()->id);
            $name = $value->get_form_element_name();
            $raw = $mode === FORMAT_HTML ? '<strong>First</strong><br>Last' : "First\nLast";
            $value->instance_form_save((object)[$name => $raw, $name . '_format' => $mode,
                $name . '_alignment' => 'C', $name . '_verticalanchor' => 'middle']);
            $saved = \core_customfield\data_controller::create($value->get('id'));
            $this->assertSame($raw, $saved->get_value());
            $this->assertSame('middle', $saved->get_vertical_anchor());
            $this->assertSame('middle', text::export_vertical_anchor($saved->export_value()));
            $saved->instance_form_save((object)[$name => $raw]);
            $this->assertSame('middle', $saved->get_vertical_anchor());
            $defaults = new \stdClass();
            $saved->instance_form_before_set_data($defaults);
            $this->assertSame('middle', $defaults->{$name . '_verticalanchor'});
            $this->assertArrayNotHasKey($name . '_verticalanchor', $saved->instance_form_validation(
                [$name => $raw, $name . '_verticalanchor' => 'middle'], []));
        }
    }
}
