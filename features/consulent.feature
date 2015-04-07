@consultant
Feature: Consultant

  Background:
    Given there are the following users:
      | email               | password | group |
      | testkons@bellcom.dk | test     | 1     |

  Scenario: Login
    Given I am on the homepage
    When I fill in "_username" with "testkons@bellcom.dk"
      And I fill in "_password" with "test"
      And I press "Log ind"
    Then I should see "Aktuel leveringstid:"
