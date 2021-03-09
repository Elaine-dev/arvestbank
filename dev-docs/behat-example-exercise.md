# Behat Code Coverage

As a site admin, I want to my FAQ content type to be under code coverage. 

Scenario 1: Behat test \
As a site admin, \
When I am looking at tests/features/ \
then I will see a faq.feature

Scenario 2: Behat test context \
As a site admin, \
When I am looking at faq.feature \
then I will see a behat test that tests the following features \
() title field question \
() a text field answer


## Exercise 

### Step One: Review User Story and Acceptance criteria 
Full review to understand the task as hand and how to test the behat test will validate the acceptance criteria.  

### Step Two: Create a new behat test
Navigate to tests/behat and create a new test feature [username]-faq.feature

### Step Three: 
Create the Feature statement 

### Step Four: If necessary create a background 
Create the Scenario 

### Step Five: Create the Scenario 
```
@api
Feature: As an admin user,
  I should be able to create faq content type

  Background:
    Given "faq_category" terms:
      | name         |
      | radiation    |
      | Seasonal Flu |

  Scenario: Create faq page with required fields
    And I am logged in as a user with the "administrator" role
    And I am on "/node/add/faq"
    And I should see a "Question" field
    And I should see an "Answer" field
    When I press "edit-submit"
    Then I should see the following success messages:
      | Faq has been created. |

```

### Step Six: Run the test locally
```
$ blt tests:behat:run -D behat.paths=${PWD}/tests/behat/features/[username]-faq.feature
```
### Step Seven: Create a new branch
```
$ git checkout -b SCC-007-first-behat-test
$ git add /tests/behat/features/[username]-faq.feature
$ git commit -m"SCC-004: Creating my first behat test."
$ git push --set-upstream origin SCC-007-first-behat-test
```

### Step Eight: Create a pull request from your origin to the upstream
Go to your github repo \
and click create a PR. \
Review your code changes \
Review the base branch is dev-assessment-exercises \
Create a  



--- 
## More Documentation 
* BBD Testing - https://www.drupal.org/drupalorg/docs/build/bdd-tests/writing-bdd-tests
* BLT testing documentation - https://docs.acquia.com/blt/developer/testing/
* Review all Drupal Behat extension option - vendor/drupal/drupal-extension/features/bootstrap/FeatureContext.php
