Feature: Shop homepage
  Background:
    Given I am on frontpage

  Scenario: Select danish website
    When I follow "Denmark"
    Then I should see "Min side"
