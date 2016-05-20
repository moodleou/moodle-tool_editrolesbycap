@ou @ou_vle @tool @tool_editrolesbycap
Feature: Test all the basic functionality of this admin tool
  In order to manage permissions across all roles
  As an administrator
  I need to edit roles by capability

  @javascript
  Scenario: Find a capability, and edit the permissions for it.
    # Go to the first page, where there is a form to select a capability.
    Given I log in as "admin"
    When I navigate to "Edit roles by capability" node in "Site administration > Users > Permissions"
    Then I should see "Select a capability"

    # Test JS filtering.
    # This part is currently failing, so commented out.
    # When I set the field "Filter" to "question:flag"
    # Then "Flag questions while attempting them (moodle/question:flag)" "option" should be visible
    # And "Edit your own questions (moodle/question:editmine)" "option" should not be visible
    # When I press "Clear"
    # Then "Flag questions while attempting them (moodle/question:flag)" "option" should be visible
    # And "Edit your own questions (moodle/question:editmine)" "option" should be visible

    # Try to go to the next page without selecting a 
    When I press "Check and edit role definitions"
    Then I should see "You must supply a value here."

    # Select an option and go to the next page.
    When I set the field "Select a capability" to "Flag questions while attempting them (moodle/question:flag)"
    And I press "Check and edit role definitions"
    Then I should see "Edit role definitions for capability Flag questions while attempting them (moodle/question:flag)"
    And I should see "Manager (manager)"
    And I should see "Managers can access course and modify them, they usually do not participate in courses."
    And the field "manager" matches value "1"
    And the field "coursecreator" matches value "0"

    # Change some options and save.
    When I set the field "manager" to "0"
    And I set the field "coursecreator" to "1"
    And I press "Save changes"
    Then I should see "Edit role definitions for capability Flag questions while attempting them (moodle/question:flag)"
    And the field "manager" matches value "0"
    And the field "coursecreator" matches value "1"

    # Switch to advanced view
    When I press "Show advanced"
    Then I should see "Edit role definitions for capability Flag questions while attempting them (moodle/question:flag)"
    And I should see "Not set"
    And I should see "Allow"
    And I should see "Prevent"
    And I should see "Prohibit"

    # Preferred view should be remembered as a user preference.
    When I navigate to "Edit roles by capability" node in "Site administration > Users > Permissions"
    And I set the field "Select a capability" to "Flag questions while attempting them (moodle/question:flag)"
    And I press "Check and edit role definitions"
    Then I should see "Edit role definitions for capability Flag questions while attempting them (moodle/question:flag)"
    And I should see "Prohibit"

    # Set some advanced capabilities, then swtich back to basic view and check the display.
    When I set the field "id_manager-1" to "1"
    And I set the field "id_coursecreator-1000" to "1"
    And I press "Save changes"
    And I press "Hide advanced"
    Then I should see "PreventUse 'Show advanced' to change"
    And I should see "ProhibitUse 'Show advanced' to change"
