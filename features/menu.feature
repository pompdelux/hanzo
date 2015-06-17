Feature: Menu

  # All pages are created beneath "Kundeservice"
  Background:
    Given the following menu items exist:
     | title             | active | on_mobile | only_mobile |
     | VisibleAll        | 1      | 1         | 0           |
     | VisibleDesktop    | 1      | 0         | 0           |
     | VisibleMobileOnly | 1      | 1         | 1           |

  Scenario: See menu item
    Given I am on the homepage
    When I hover over the element "li a.page-609"
    Then I should see "VisibleAll"

  Scenario: Not see mobile only element
    Given I am on the homepage
    When I hover over the element "li a.page-609"
    Then I should not see "VisibleMobileOnly"

  @mobile
  Scenario: See mobile only element
    Given I am on the homepage
    When I click on the element ".menu-trigger"
     And I wait until the entire menu is visible
    Then I should see "VisibleMobileOnly"
