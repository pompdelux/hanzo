Feature: RMA Claims form
  Background:
    Given I am on "forside/kundeservice/returnering"

  Scenario: Fill out claims forms
      When I fill in the following:
        | name            | Mit navn            |
        | customer_number | 100000              |
        | order_number    | 100                 |
        | product_info    | En style            |
        | description     | En kort beskrivelse |
        | contact         | email               |
      And I attach the file "Lorem-ipsum.pdf" to "pictures[]"
      And I press "Send reklamationsformular"
      Then I should see "Din reklamationsformular er nu sendt"

  @wip
  Scenario: Fill out claims forms
      When I press "Send reklamationsformular"
      Then I should not see "Din reklamationsformular er nu sendt"
