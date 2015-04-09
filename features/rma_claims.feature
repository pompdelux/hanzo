Feature: RMA Claims form
  Background:
    Given I am on "forside/kundeservice/returnering"

  Scenario: Fill out claims forms
      When I fill in the following:
        | name            | Mit navn            |
        | order_number    | 100                 |
        | product_info    | En style            |
        | description     | En kort beskrivelse |
        | contact         | email               |
        | email_value     | test@bellcom.dk     |
      And I attach the file "Lorem-ipsum.pdf" to "picture_1"
      And I press "Send reklamationsformular"
      And I wait until Ajax is done
      Then I should see "Din reklamationsformular er nu sendt"

  @wip
  Scenario: Fill out claims forms
      When I press "Send reklamationsformular"
      Then I should not see "Din reklamationsformular er nu sendt"
