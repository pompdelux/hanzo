Feature: Product filtering

  Background:
    Given I am on a category page

  Scenario: See the available filters
    Then I should see "Farve"
     And I should see "Størrelse"
     And I should see "OEKO-Tex/Økologi"
     And I should see "Rabat"

  Scenario: See color options
    When I click the element ".filter-color-toggle"
    Then I should see "Rose"

  Scenario: See size options
    When I click the element ".filter-size-toggle"
    Then I should see "80"

  Scenario: See eco options
    When I click the element ".filter-eco-toggle"
    Then I should see "Økologi (GOTS)"

  Scenario: See discount options
    When I click the element ".filter-discount-toggle"
    Then I should see "10%"

  Scenario: Select options
    When I click the element ".filter-color-toggle"
     And I check "color_Rose"
    Then I should see "Rose" in the ".filter-text > span" element
     And I should see "Ryd alt"

  Scenario: Select and clear
    When I click the element ".filter-color-toggle"
     And I check "color_Rose"
     And I click the element ".js-filter-clear"
    Then I should not see "Ryd alt"
