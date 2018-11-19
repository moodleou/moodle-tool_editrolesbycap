# Edit roles by capability tool [![Build Status](https://travis-ci.org/moodleou/moodle-tool_editrolesbycap.svg?branch=master)](https://travis-ci.org/moodleou/moodle-tool_editrolesbycap)

Normally, when you edit roles in Moodle, you select one role, and can then edit
the permissions for all the capabilities for that role. This plugin presents an
alterative interface. You can select one capability, and then edit the
permissions for all roles for that capability.

You will get a new entry under Site administration -> Users -> Permissions that
lets you select a capability, and then edit the permission for that capability
in all role definition.


## Acknowledgements

This plugin was created by the Open University, UK. http://www.open.ac.uk/


## Installation and set-up

This plugin should be compatible with Moodle 3.4+.

### Install from the plugins database

See https://moodle.org/plugins/tool_editrolesbycap.

### Install using git

Or you can install using git. Type this commands in the root of your Moodle install

    git clone git://github.com/moodleou/moodle-tool_editrolesbycap.git admin/tool/editrolesbycap
    echo '/admin/tool/editrolesbycap/' >> .git/info/exclude

Then run the moodle update process
Site administration > Notifications
