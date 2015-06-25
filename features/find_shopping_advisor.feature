Feature: Find a shopping advisor
  As a customer
  I should be able to search for a shopping advisor

  Scenario: Search for shopping advisor
    Given I am on "forside/homeshopping/find-shopping-advisor"
      And I fill in "geo-zipcode" with "6000"
      And I press "Find"
      And I wait until Ajax is done
     Then I should see "Email" in the "#near-you-container" element
      And I should see "Kortdata"
