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
 * Add the edit roles by capability tool to the admin menus.
 *
 * @package    tool_editrolesbycap
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if (has_capability('moodle/role:manage', context_system::instance())) {
    $ADMIN->add('roles', new admin_externalpage('tooleditrolesbycap',
            get_string('pluginname', 'tool_editrolesbycap'),
            new moodle_url('/admin/tool/editrolesbycap/index.php'),
            'moodle/role:manage'));
}
