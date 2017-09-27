<img src="https://crossbrowsertesting.com/design/images/brand/cbt-sb-logo.svg" width="50%">

----

# Behat and CBT

[Behat](http://behat.org/en/latest/) is a [Behavior Driven Development](https://dannorth.net/introducing-bdd/) test framework for PHP. The power of Behat, and BDD in general, is that your tests define how your application should *behave* in different *scenarios*. More than just confirming that parts of your application work, BDD helps define and clarify the overall design of your application. And since Behat/BDD tests are writen in [plain English](http://docs.behat.org/en/v2.5/guides/1.gherkin.html), they can be understood by devs, managers, marketers, and everyone else involved with your application. 

## Getting set up

### Installing Composer

First, make sure you have PHP Dependency Manager [**Composer**](https://getcomposer.org/) installed. Follow the instructions on their [download page](https://getcomposer.org/download/) to install it.

### Installing Behat

Once you have Composer, the easiest way to get set up would be to clone this repository, then run `composer install` to install the modules listed in `composer.json` (and their dependencies). 

### Setting up conf.yml files

In order to run any tests, you'll need to make a few changes to the configuration files located in `config/`.

In order to run tests, you'll need a valid credentials, so set `user` to the email address associated with your crossbrowsertesting.com account, and set `key` to your authkey. The easiest way to find your authkey by going to your [account page](https://crossbrowsertesting.com/account).

You can also change the `capabilities` and `browers` keys to rename/re-version your test, extend the `max_duration`, or change your target browser(s).

To find the os_api_name and browser_api_name that correspond to your targeted platform, you can either go to CrossBrowserTesting's [Selenium Page](https://app.crossbrowsertesting.com/selenium/run), open the wizard, and watch the capabilities in the sample script change as you select different browers, OR you can parse the os/browser you want out of the JSON response from https://crossbrowsertesting.com/api/v3/selenium/browsers

If you run into any trouble or have any questions, send an email to info@crossbrowsertesting.com.

## Writing tests

Now that the environment is set up, we can start actually writing tests. There are two main steps to writing test with Behat: define application behavior as scenarios and steps, then code each step as a function. 

### Defining behavior in `*.feature` files

Accurately and concisely describing application behavior is the core of BDD. Behat (and most other BDD frameworks) use the [Gherkin](https://github.com/cucumber/cucumber/wiki/Gherkin) language to write real automated tests in plain English. 

Let's walk through writing a couple simple tests for our example [login page](http://crossbrowsertesting.github.io/login-form.html). It's a simple page, so our tests won't be very long.

Let's start by defining what our feature is in `features/login.feature` 

```gherkin
Feature: Login to page 
    As a user 
    I should be able to log in 
    If I supply valid user credentials
```

Simple enough. The whole block will be displayed at the start of the test, so make sure that it says something useful. 


Next, we define how our application should behave in different scenarios. For this simple login page there are only two scenarios we need to test: a valid login, and an invalid login.

```gherkin
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
```

As you can see, we use plain english to describe the setup (being on the right page), the actions for the test (filling in username and password then clicking the login button), and the desired outcome (the message we see). 

The Gherkin language can handle some very complicated test logic, so I recommend taking a look at Behat's official guide to [writing features with Gherkin](http://docs.behat.org/en/v2.5/guides/1.gherkin.html).

### Coding steps in the `FeatureContext.php` file

Now that our feature is defined, we can run our tests with `$ bin/behat`. 

You should see an output like this:

```
2 scenarios (2 undefined)
10 steps (10 undefined)
0m0.011s

You can implement step definitions for undefined steps with these snippets:

    /**
     * @Given /^I go to "([^"]*)"$/
     */
    public function iGoTo($arg1)
    {
        throw new PendingException();
    }

    [...]

```

So, we can see that Behat parsed `login.feature` but wasn't able to find functions in `features/bootstrap/FeatureContext.php` to tell it how to execute each step. 

But we can copy the functions it generates and paste them into our `FeatureContext.php` file to give us a starting place. 

The comment above each function describes the Regex that it uses to link steps in the `login.feature` file to the function, as well as how arguments get passed from the step to the function.

Now we just have to write some Selenium code to define each step! This isn't a Selenium guide, so I'll just point you towards Facebook's [excellent documentation](https://facebook.github.io/php-webdriver/). If you encounter any trouble, be sure to shoot an email to info@crossbrowsertesting.com and we'll be able to give you a hand.

## Running tests

Now that everything is set up, let's try running the test! 

```
$ bin/behat -c config/single.conf.yml

Feature: Test a login form

  Scenario: Login with bad credentials                                        # /Users/johnreese/Google Drive/CBT/repos/behat-browserstack/features/single/login.feature:3
    Given I go to "http://crossbrowsertesting.github.io/login-form.html"      # FeatureContext::iGoTo()
    When I fill in "#username" with "badusername@crossbrowsertesting.com"     # FeatureContext::iFillInWith()
    And I fill in "#password" with "badpassword"                              # FeatureContext::iFillInWith()
    And I press ".form-actions > button"                                      # FeatureContext::iPress()
    Then I should see ".alert-danger" say "Username or password is incorrect" # FeatureContext::iShouldSeeSay()

  Scenario: Login with good credentials                                       # /Users/johnreese/Google Drive/CBT/repos/behat-browserstack/features/single/login.feature:10
    Given I go to "http://crossbrowsertesting.github.io/login-form.html"      # FeatureContext::iGoTo()
    When I fill in "#username" with "tester@crossbrowsertesting.com"          # FeatureContext::iFillInWith()
    And I fill in "#password" with "test123"                                  # FeatureContext::iFillInWith()
    And I press ".form-actions > button"                                      # FeatureContext::iPress()
    Then I should see "#logged-in-message>p" say "You are now logged in!"     # FeatureContext::iShouldSeeSay()

2 scenarios (2 passed)
10 steps (10 passed)
0m43.527s

```

Success!

### Parallel execution

But how can we run the test on multiple browsers at once? Easy! This repo includes a file called `run-parallel.php` which can help manage parallel execution across multiple browsers at once. 

I've included a configuration file that will run tests on IE8 through IE11 as well as Edge called `ie.conf.yml`. It is identical to `single.conf.yml`, except that it lists more than one browser.

To start the test, just run our helper file like this:

```
$ php run-parallel.php -c config/ie.conf.yml
```

### Websites behind your firewall

If you have our Node module `cbt_tunnels` installed, you can run tests to websites behind your firewall by running `run-parallel.php` with the -l flag. 

If you don't have `cbt_tunnels` installed, you can install it by running `$ npm install -g cbt_tunnels`.

## Help! 

If you got stuck, or something doesn't make sense, don't worry! Just shoot an email to info@crossbrowsertesting.com and we'll help you out. 

### Happy testing!
