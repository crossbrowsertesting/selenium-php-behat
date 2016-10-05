Feature: Login to page 
    A login attempt with valid credentials
    Should be accepted
    And a login attempt with bad credentials
    Should be rejected

    Scenario: Login with bad credentials
        Given I go to "http://crossbrowsertesting.github.io/login-form.html"
        When I fill in "#username" with "badusername@crossbrowsertesting.com"
        And I fill in "#password" with "badpassword"
        And I press ".form-actions > button" 
        Then I should see ".alert-danger" say "Username or password is incorrect" 

    Scenario: Login with good credentials
        Given I go to "http://crossbrowsertesting.github.io/login-form.html"
        When I fill in "#username" with "tester@crossbrowsertesting.com"
        And I fill in "#password" with "test123"
        And I press ".form-actions > button" 
        Then I should see "#logged-in-message>p" say "You are now logged in!" 
