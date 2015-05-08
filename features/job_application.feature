Feature: Job application
  Background:
    Given I am on "test-jobansoegning"

  Scenario: Fill out form
      When I fill in the following:
        | name                    | Mit navn            |
        | address                 | Testvej 1           |
        | zipcode                 | 6000                |
        | city                    | Kolding             |
        | phone                   | 12345678            |
        | email                   | test@bellcom.dk     |
        | describe_yourself       | Test beskrivelse 1  |
        | describe_motivation     | Test motivation     |
      And I attach the file "Lorem-ipsum.pdf" to "images_1"
      And I press "Send din ansøgning"
      And I wait until Ajax is done
      Then I should see "Din ansøgning er nu sendt."