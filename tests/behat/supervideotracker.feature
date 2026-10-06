@mod @mod_supervideotracker
Feature: Create and open a Super Video Tracker package
  In order to track several videos as one compliance package
  As a teacher
  I need to create the activity and reach its media management screen

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Compliance course | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | One | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  Scenario: Create the package
    Given I log in as "teacher1"
    And I am on "Compliance course" course homepage with editing mode on
    When I add a "Super Video Tracker" to section "1" and I fill the form with:
      | Name | Mandatory training |
    Then I should see "Mandatory training"
    And I should see "Overall progress"
