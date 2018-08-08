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
 * Library code for the edit roles by capability tool.
 *
 * @package    tool_editrolesbycap
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/capabilityformfield.php');

MoodleQuickForm::registerElementType('capability',
    dirname(__FILE__) . '/capabilityformfield.php',
    'MoodleQuickForm_capability');


/**
 * Editing form to let the user select a capability.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_editrolesbycap_capability_form extends moodleform {
    protected function definition() {
        if (empty($this->_customdata['cap'])) {
            $label = get_string('selectacapability', 'tool_editrolesbycap');
        } else {
            $label = get_string('selectadifferentcapability', 'tool_editrolesbycap');
        }

        $this->_form->addElement('capability', 'cap', $label, array("size" => 10));
        $this->_form->addRule('cap', null, 'required', null, 'client');

        $this->add_action_buttons(false, get_string('checkandeditroles', 'tool_editrolesbycap'));
    }
}

/**
 * Get what would be the default setting for this capability for this role.
 *
 * @param object $role the role.
 * @param object $capability the capability.
 * @return int one of the CAP_... constants.
 */
function tool_editrolesbycap_get_default_permission($role, $capability) {
    static $cache = array();

    if (empty($role->archetype)) {
        return CAP_INHERIT;
    }

    if (isset($cache[$capability->name][$role->archetype])) {
        return $cache[$capability->name][$role->archetype];
    }

    $defaults = get_default_capabilities($role->archetype);
    if (isset($defaults[$capability->name])) {
        $cache[$capability->name][$role->archetype] = $defaults[$capability->name];
    } else {
        $cache[$capability->name][$role->archetype] = CAP_INHERIT;
    }

    return $cache[$capability->name][$role->archetype];
}

/**
 * Load all system-context role_capabilities for a give capability.
 *
 * @param stdClass $capability the information about a capability.
 * @return array role shortname => object with fields (role) shortname, (role) name,
 *      (role) description and permission (for this capability for that role).
 */
function tool_editrolesbycap_load_role_definitions($capability) {
    global $DB;

    $data = $DB->get_records_sql('
                SELECT r.id AS roleid,
                       r.shortname,
                       r.name,
                       r.description,
                       r.archetype,
                       rc.permission
                  FROM {role} r
             LEFT JOIN {role_capabilities} rc ON rc.roleid = r.id
                                             AND rc.capability = :capability
                                             AND rc.contextid = :syscontextid
              ORDER BY r.sortorder, r.name',
          array('capability' => $capability->name,
                'syscontextid' => context_system::instance()->id));

    foreach ($data as $role) {
        $role->name = role_get_name($role);
        $role->description = role_get_description($role);
        $role->defaultpermission = tool_editrolesbycap_get_default_permission($role, $capability);
    }

    return $data;
}
