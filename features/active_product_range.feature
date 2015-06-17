Feature: Active product range
  As a customer
  I should only see products from a specific product range if I edit an order placed on the consultant site
  and the order contains products from another product range

  Background:
    Given I am logged in as customer
      And there are no orders
      And a consultant creates an order with the following products:
        | name      | range      |
        | Testpants | TEST_RANGE |
      And I edit the order

  Scenario: Only see products that is in the product range of the order I am editing
    Given I am on a category page
     Then I should only see products which are in the active product range
