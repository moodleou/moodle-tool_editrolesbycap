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
 * Edit roles by capability tool.
 *
 * This tool lets the adminstrator edit the definition for all roles for one
 * particular capability. So, for example, suppose you want to make sure that
 * only certain roles have the mod/quiz:preview capability, then this is the
 * tool for you. You can do that on one page, without having to click through
 * the pages that show each different role definition.
 *
 * @package    tool_editrolesbycap
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

$cap = optional_param('cap', '', PARAM_CAPABILITY);
$capability = get_capability_info($cap);

require_login();
$context = context_system::instance();
require_capability('moodle/role:manage', $context);

$showadvanced = get_user_preferences('definerole_showadvanced', false);
if (optional_param('toggleadvanced', false, PARAM_BOOL)) {
    $showadvanced = !$showadvanced;
    set_user_preference('definerole_showadvanced', $showadvanced);
}

$params = array();
if ($capability) {
    $capability->name = $cap;
    $params['cap'] = $capability->name;
}
admin_externalpage_setup('tooleditrolesbycap', '', $params);

$form = new tool_editrolesbycap_capability_form(
        new moodle_url('/admin/tool/editrolesbycap/index.php'), $params);
$form->set_data($params);

if ($data = $form->get_data()) {
    redirect(new moodle_url('/admin/tool/editrolesbycap/index.php', array('cap' => $data->cap)));
}

$renderer = $PAGE->get_renderer('tool_editrolesbycap');

if ($capability) {
    $roledata = tool_editrolesbycap_load_role_definitions($capability);

    if (data_submitted() && confirm_sesskey()) {
        $savechanges = optional_param('savechanges', null, PARAM_BOOL);
        if ($savechanges) {
            $transaction = $DB->start_delegated_transaction();
        }

        foreach ($roledata as $role) {
            $newpermission = optional_param($role->shortname, null, PARAM_PERMISSION);

            if ($savechanges && $newpermission != $role->permission) {
                assign_capability($capability->name, $newpermission,
                        $role->roleid, $context->id, true);
            }

            $role->permission = $newpermission;
        }

        if ($savechanges) {
            $transaction->allow_commit();
            redirect($PAGE->url);
        }
    }

    echo $renderer->index_page_capability_selected($form, $capability, $roledata, $showadvanced);

} else {
    echo $renderer->index_page_no_capability($form);
}
