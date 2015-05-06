Feature: Page with discount filter

  Background:
    Given I am on a category page

  Scenario: See discount filter page
    Then I should see "Rabat"

  Scenario: See discount options
    When I hover over the element "js-filter-type-discount"
    Then I should see "50%"
