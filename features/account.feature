Feature: Customer login and create account

  Scenario: Login
    Given I am on "login"
    When I fill in "_username" with "hf+test1@bellcom.dk"
      And I fill in "_password" with "farre"
      And I press "Log ind"
    Then I should see "Velkommen Test!"
      And I should see "Se eller ret kontoinformationer."

  # Only for da_DK and nl_NL
  Scenario: Create Account
    Given I am on "account/create"
    Then the "customers_newsletter" checkbox should not be checked

  # Should be checked on other countries
  @de_DE
  Scenario: Create Account
    Given I am on "account/create"
    Then the checkbox "customers_newsletter" should be checked
