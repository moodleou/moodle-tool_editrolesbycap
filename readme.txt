Edit roles by capability tool
https://moodle.org/plugins/tool_editrolesbycap

Created by the Open University, UK.

Normally, when you edit roles in Moodle, you select one role, and can then edit
the permissions for all the capabilities for that role. This plugin presents an
alterative interface. You can select one capability, and then edit the
permissions for all roles for that capability.

This is version 1.5 of this plugin, which works with Moodle 3.2+.

Install this plugin into the admin/tool folder, in a subfolder called
editrolesbycap. You can do that using git as

    git clone git://github.com/moodleou/moodle-tool_editrolesbycap.git admin/tool/editrolesbycap
    echo '/admin/tool/editrolesbycap/' >> .git/info/exclude

You will get a new entry under Site administration -> Users -> Permissions that
lets you select a capability, and then edit the permission for that capability
in all role definition.
