Feature: Search for a product
  Background:
    Given I am on "search"

  Scenario: Search result
    When I fill in "advanched_search_query" with "evry"
      And I press "perform_search"
    Then I should see "Fundet 1 produkter"
