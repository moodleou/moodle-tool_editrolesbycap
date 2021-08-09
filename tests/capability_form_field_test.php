<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/editrolesbycap/capabilityformfield.php');


/**
 * Test of the custom form field class.
 *
 * @package tool_editrolesbycap
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class capability_form_field_test extends advanced_testcase {

    public function test_all_capabilities_included() {
        $field = new MoodleQuickForm_capability('cap');

        $groupname = context_helper::get_level_name(CONTEXT_MODULE) . ': ' .
                get_component_string('mod_h5pactivity', CONTEXT_COURSE);
        $hvpcourselevelcaps = null;
        foreach ($field->_optGroups as $group) {
            if ($group['attr']['label'] == $groupname) {
                $hvpcourselevelcaps = array_map(
                        function($a) {
                            return $a['attr']['value'];
                        },
                        $group['options']);
                break;
            }
        }
        $this->assertContains('mod/h5pactivity:view', $hvpcourselevelcaps);
    }
}
