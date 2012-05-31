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

/**
 * A formslib field type for selecting one capability.
 *
 * @package    core_form
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir . '/form/selectgroups.php');


/**
 * A formslib field type for selecting one capability.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_capability extends MoodleQuickForm_selectgroups {
    public function MoodleQuickForm_capability($elementname = null, $elementlabel = null,
            $attributes = array(), $showchoose = false) {

        parent::__construct($elementname, $elementlabel,
                $this->get_capabitity_optgroups(), $attributes, $showchoose);
    }

    protected function get_capabitity_optgroups() {
        if (!empty($this->_optGroups)) {
            // I have absolutely no idea why this is necessary, but it does seem to be.
            // Bloody formslib. Somehow it is calling the constructor twice.
            return array();
        }

        $optgroups = array();
        $capabilities = context_system::instance()->get_capabilities();

        $contextlevel = 0;
        $component = '';
        $currentgroup = array();
        $currentgroupname = '';
        foreach ($capabilities as $capability) {
            // Start a new optgroup if the componentname or context level has changed.
            if (component_level_changed($capability, $component, $contextlevel)) {
                if ($currentgroup) {
                    $optgroups[$currentgroupname] = $currentgroup;
                }
                $currentgroup = array();
                $currentgroupname = context_helper::get_level_name($capability->contextlevel) . ': ' .
                        get_component_string($capability->component, $capability->contextlevel);
            }
            $contextlevel = $capability->contextlevel;
            $component = $capability->component;

            $a = new stdClass();
            $a->name = get_capability_string($capability->name);
            $a->capabilityname = $capability->name;
            $currentgroup[$capability->name] = get_string('capabilityandname',
                    'tool_editrolesbycap', $a);
        }

        // Remeber to add the currently open optgroup
        if ($currentgroup) {
            $optgroups[$currentgroupname] = $currentgroup;
        }

        return $optgroups;
    }

    public function toHtml() {
        global $PAGE;
        $this->_generateId();
        if (!$this->_flagFrozen) {
            $PAGE->requires->string_for_js('nonematch', 'tool_editrolesbycap');
            $PAGE->requires->string_for_js('filter', 'moodle');
            $PAGE->requires->string_for_js('clear', 'moodle');
            $PAGE->requires->yui_module('moodle-tool_editrolesbycap-capabilityformfield',
                    'M.tool_editrolesbycap.init_capabilityformfield',
                    array('#' . $this->getAttribute('id')));
        }
        return parent::toHtml();
    }
}
