Feature: Wish list
  As a customer
  I should be able to create and edit a wish list

  # Todo: clear wish lists before running
  Background:
    Given I am logged in as customer
    And I am on "account/wishlist/create"

  Scenario: Add a product to the wish list
    When I type "Nador Lt langærmet grandad" into the autocomplete field "form-item-q"
      And I wait for the suggestion box to appear
      And I click the element ".tt-suggestions > .tt-suggestion"
      And I wait until Ajax is done
      And I select "80" from "size"
      And I wait until Ajax is done
      And I select "Grey Melange" from "color"
      And I press "Tilføj til listen"
      And I wait until Ajax is done
    Then I should see "Samlet Beløb: DKK 117,00"

    # TODO: edit / delete / reset
