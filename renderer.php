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
 * Defines the renderer for the edit roles by capability tool.
 *
 * @package    tool_editrolesbycap
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Renderer for the edit roles by capability tool.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_editrolesbycap_renderer extends plugin_renderer_base {

    /**
     * Render the tool before a capability has been selected.
     *
     * @param tool_editrolesbycap_capability_form $form the form that lets the user select a capability.
     * @return string the HTML to output.
     */
    public function index_page_no_capability(tool_editrolesbycap_capability_form $form) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('pluginname', 'tool_editrolesbycap'));
        $output .= $this->capability_form($form);
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render the tool when a capability is selected.
     * @param moodleform $form the form that lets the user select a capability.
     * @param stdClass $capability information about the currently selected capability.
     * @param array $roledata data about how the capability is set for each role.
     * @param bool $showadvanced whether to show the advanced UI or not.
     * @return string the HTML to output.
     */
    public function index_page_capability_selected(moodleform $form, $capability, array $roledata, $showadvanced) {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('pluginname', 'tool_editrolesbycap'));
        $output .= $this->heading(get_string('editrolesfor', 'tool_editrolesbycap',
                $this->capability_name_full($capability)), 3);
        $output .= $this->role_definitions($roledata, $showadvanced);
        $output .= $this->capability_form($form);
        $output .= $this->footer();
        return $output;
    }

    /**
     * Render a formslib form.
     * @param moodleform $form
     * @return string the HTML to output.
     */
    protected function capability_form(moodleform $form) {
        ob_start();
        $form->display();
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * Render the role definitions as a form where the permissions can be changed.
     * @param array $roledata data about how the capability is set for each role.
     * @param bool $showadvanced whether to show the advanced UI or not.
     * @return string the HTML to output.
     */
    protected function role_definitions(array $roledata, $showadvanced) {
        $table = new html_table();
        $table->attributes['class'] = 'roledefs generaltable rolecap';
        $table->head = array(
            get_string('role'),
            get_string('permission', 'role') . ' ' . $this->help_icon('permission', 'role'),
            get_string('roledescription', 'tool_editrolesbycap'),
        );
        if ($showadvanced) {
            $table->headspan = array(1, 4, 1);
        }

        foreach ($roledata as $role) {
            $role->name = format_string($role->name);

            $row = new html_table_row();
            $row->cells[] = new html_table_cell(get_string('rolenameandshortname', 'tool_editrolesbycap', $role));
            $this->add_permission_cells($row, $role, $showadvanced);
            $row->cells[] = new html_table_cell(format_text($role->description, FORMAT_HTML));
            $table->data[] = $row;
        }

        $output = '';
        $output .= $this->show_hide_advanced_button($showadvanced);
        $output .= html_writer::table($table);
        $output .= $this->save_changes_button();

        $attributes = array(
            'action' => $this->page->url,
            'method' => 'post',
        );
        return html_writer::tag('form', $output, $attributes);
    }

    /**
     * Render a form element for editing the permission for one role.
     * @param html_table_row $row the table row being assembled.
     * @param stdClass $role the role information.
     * @param bool $showadvanced whether to show the advanced UI or not.
     */
    protected function add_permission_cells($row, $role, $showadvanced) {
        if ($showadvanced) {
            $this->add_permission_cells_advanced($row, $role);
        } else if ($role->permission == CAP_INHERIT || $role->permission == CAP_ALLOW) {
            $this->add_permission_cells_basic($row, $role);
        } else {
            $this->add_permission_cells_basic_uncommon_permission($row, $role);
        }
    }

    /**
     * Render a form element for editing the permission for one role - basic view.
     * @param html_table_row $row the table row being assembled.
     * @param stdClass $role the role information.
     */
    protected function add_permission_cells_basic($row, $role) {
        $output = '';

        $attributes = array(
            'type'  => 'hidden',
            'name'  => $role->shortname,
            'value' => CAP_INHERIT,
        );
        $output .= html_writer::empty_tag('input', $attributes);

        $attributes['type']  = 'checkbox';
        $attributes['value'] = CAP_ALLOW;
        $attributes['id']    = 'id_' . $role->shortname;
        if ($role->permission == CAP_ALLOW) {
            $attributes['checked'] = 'checked';
        }

        $output .= html_writer::tag('label',
                html_writer::empty_tag('input', $attributes) . $this->permission_name(CAP_ALLOW),
                array('for' => $attributes['id']));
        $cell = new html_table_cell($output);

        if ($role->defaultpermission == CAP_ALLOW) {
            $cell->attributes['class'] = 'capdefault allow';
        } else {
            $cell->attributes['class'] = 'allow';
        }

        $row->cells[] = $cell;
    }

    /**
     * Render a form element for editing the permission for one role - basic view.
     * @param html_table_row $row the table row being assembled.
     * @param stdClass $role the role information.
     */
    protected function add_permission_cells_basic_uncommon_permission($row, $role) {
        $output = '';

        $attributes = array(
            'type'  => 'hidden',
            'name'  => $role->shortname,
            'value' => $role->permission,
        );
        $output .= html_writer::empty_tag('input', $attributes);
        $output .= $this->permission_name($role->permission);
        $output .= html_writer::tag('span', get_string('useshowadvancedtochange', 'role'), array('class' => 'note'));

        $cell = new html_table_cell($output);
        $cell->attributes['class'] = 'allow';
        $row->cells[] = $cell;
    }

    /**
     * Render a form element for editing the permission for one role - advanced view.
     * @param html_table_row $row the table row being assembled.
     * @param stdClass $role the role information.
     */
    protected function add_permission_cells_advanced($row, $role) {

        // One cell for each possible permission.
        foreach ($this->permission_names() as $permission => $permissionname) {
            $output = '';

            $attributes = array(
                'type'  => 'radio',
                'name'  => $role->shortname,
                'value' => $permission,
                'id'    => 'id_' . $role->shortname . $permission,
            );
            if ($permission == $role->permission) {
                $attributes['checked'] = 'checked';
            }

            $output .= html_writer::tag('label',
                    html_writer::empty_tag('input', $attributes) .
                    html_writer::tag('span', $permissionname, array('class' => 'note')),
                    array('for' => $attributes['id']));

            $cell = new html_table_cell($output);

            // Using the 'allow' class name instead of inherit is a bit of a
            // hack, but it gets us the styling we want.
            if ($permission == $role->defaultpermission) {
                $cell->attributes['class'] = 'capdefault allow';
            } else {
                $cell->attributes['class'] = 'allow';
            }

            $row->cells[] = $cell;
        }
    }

    /**
     * Render the show/hide advanced button.
     * @param bool $showadvanced whether to show the advanced UI or not.
     * @return string the HTML to output.
     */
    protected function show_hide_advanced_button($showadvanced) {
        $attributes = array(
            'type'  => 'submit',
            'name'  => 'toggleadvanced',
            'class'  => 'btn btn-secondary',
        );

        if ($showadvanced) {
            $attributes['value'] = get_string('hideadvanced', 'form');
        } else {
            $attributes['value'] = get_string('showadvanced', 'form');
        }

        return html_writer::tag('div', html_writer::empty_tag('input', $attributes), array('class' => 'advancedbutton'));
    }

    /**
     * Render the show/hide advanced button.
     * @return string the HTML to output.
     */
    protected function save_changes_button() {
        $ouput = '';

        $attributes = array(
            'type'  => 'hidden',
            'name'  => 'sesskey',
            'value' => sesskey(),
        );
        $ouput .= html_writer::empty_tag('input', $attributes);

        $attributes = array(
            'type'  => 'submit',
            'name'  => 'savechanges',
            'value' => get_string('savechanges'),
            'class'  => 'btn btn-primary',
        );
        $ouput .= html_writer::empty_tag('input', $attributes);

        return html_writer::tag('div', $ouput, array('class' => 'submitbutton'));
    }

    /**
     * Get an array of conversions from the internal constants to the human-readable strings.
     *
     * @return array CAP_... constant => permission name string.
     */
    protected function permission_names() {
        static $permissionnames = null;
        if (is_null($permissionnames)) {
            $permissionnames = array(
                CAP_INHERIT  => get_string('notset', 'role'),
                CAP_ALLOW    => get_string('allow', 'role'),
                CAP_PREVENT  => get_string('prevent', 'role'),
                CAP_PROHIBIT => get_string('prohibit', 'role'),
            );
        }
        return $permissionnames;
    }

    /**
     * Get a permission name for display.
     *
     * @param int $permission one of the CAP_... constants.
     * @return string the permission name.
     */
    protected function permission_name($permission) {
        $permissionnames = $this->permission_names();
        return $permissionnames[$permission];
    }

    /**
     * Render a capabilitiy name, including docs link, internal name and risk icons.
     *
     * @param object $capability the capability.
     * @return string the HTML to output.
     */
    protected function capability_name_full($capability) {
        $a = new stdClass;
        $a->name = get_capability_docs_link($capability);
        $a->capabilityname = html_writer::tag('span', $capability->name, array('class' => 'cap-name'));
        $a->risks = $this->risk_icons($capability);
        return html_writer::tag('span', get_string('capabilitynamewithrisks', 'tool_editrolesbycap', $a),
                array('class' => 'cap-desc'));
    }

    /**
     * Render all the risk icons for a capability.
     * @param stdClass $capability the information about a capability.
     * @return string the HTML to output.
     */
    protected function risk_icons($capability) {
        $icons = array();
        foreach (get_all_risks() as $riskname => $risk) {
            if ($risk & (int)$capability->riskbitmask) {
                $icons[] = $this->risk_icon($riskname);
            }
        }
        return implode(' ', $icons);
    }

    /**
     * Render the icon for a risk.
     * @param string $type capability risk type.
     * @return string the HTML to output.
     */
    protected function risk_icon($type) {
        $url = get_docs_url(s(get_string('risks', 'role')));
        return $this->action_icon($url,
                new pix_icon('i/' . str_replace('risk', 'risk_', $type),
                        get_string($type . 'short', 'admin')),
                new popup_action('click', $url, 'docspopup'));
    }
}
