Feature: Navigation

  Scenario: Desktop - show main menu on hover
    Given I go to a product page
    When I hover over the element ".navigation-main li.first a[href=/da_DK/pige]"
    Then I should see ".navigation-main > ul > li.first > ul"
