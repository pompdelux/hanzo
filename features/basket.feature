Feature: Basket

  Scenario: Add a product to the basket
    Given I go to a product page
    When I select "80" from "size"
      And I select "Grey" from "color"
      And I select "1" from "quantity"
      And I press "Læg i kurv"
    Then I should see "(1) DKK 360,00"
