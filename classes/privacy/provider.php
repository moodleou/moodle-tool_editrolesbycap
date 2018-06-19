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
 * Privacy Subsystem implementation for tool_editrolesbycap.
 *
 * @package tool_editrolesbycap
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_editrolesbycap\privacy;
defined('MOODLE_INTERNAL') || die();
use core_privacy\local\metadata\collection;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
/**
 * Implementation of the privacy subsystem plugin provider for tool_editrolesbycap.
 *
 * @package tool_editrolesbycap
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    /**
     * Returns metadata about tool_editrolesbycap.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_user_preference('definerole_showadvanced', 'privacy:metadata:preference:definerole_showadvanced');
        return $collection;
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {
        $showadvanced = get_user_preferences('definerole_showadvanced', null, $userid);
        if (isset($showadvanced)) {
            writer::export_user_preference(
                'tool_editrolesbycap',
                'definerole_showadvanced', transform::yesno($showadvanced),
                get_string('privacy:metadata:preference:definerole_showadvanced', 'tool_editrolesbycap')
            );
        }
    }
}
