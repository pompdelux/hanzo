Feature: Product filtering

  Background:
    Given I am on a category page

  Scenario: See the available filters
    Then I should see "Farve"
     And I should see "Størrelse"
     And I should see "OEKO-Tex/Økologi"
     And I should see "Rabat"

  Scenario: See discount options
    When I click the element "filter-discount-toggle"
    Then I should see "10%"
