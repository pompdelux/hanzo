Feature: Customer login
  Background:
    Given I am on "login"

  Scenario: Login
    When I fill in "_username" with "hf+test1@bellcom.dk"
      And I fill in "_password" with "farre"
      And I press "Log ind"
    Then I should see "Velkommen Test!"
      And I should see "Se eller ret kontoinformationer."
