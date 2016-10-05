<?php

require "vendor/autoload.php";

use Behat\Behat\Context\BehatContext,
  Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext; 

class FeatureContext extends CBTContext {

  /** @Given /^I am on "([^"]*)"$/ */
  public function iAmOnSite($url) {
    self::$driver->get($url);
  }

  /** @When /^I search for "([^"]*)"$/ */
  public function iSearchFor($searchText) {
    $element = self::$driver->findElement(WebDriverBy::name("q"));
    $element->sendKeys($searchText);
    $element->submit();
    sleep(5);
  }

  /** @Then /^I get title as "([^"]*)"$/ */
  public function iShouldGet($string) {
    $title = self::$driver->getTitle();
    if ((string)  $string !== $title) {
      throw new Exception("Expected title: '". $string. "'' Actual is: '". $title. "'");
    }
  }

  /** @Then /^I should see "([^"]*)"$/ */
  public function iShouldSee($string) {
    $source = self::$driver->getPageSource();
    if (strpos($source, $string) === false) {
      throw new Exception("Expected to see: '". $string. "'' Actual is: '". $source. "'");
    }
  }

  /**
  * @Given /^I go to "([^"]*)"$/
  */
  public function iGoTo($url)
  {
    self::$driver->get($url);
  }

  /**
  * @When /^I fill in "([^"]*)" with "([^"]*)"$/
  */
  public function iFillInWith($cssSelector, $textToType)
  {
    $el = self::$driver->findElement(WebDriverBy::cssSelector($cssSelector));
    $el->click();
    $el->sendKeys($textToType);

  }

  /**
  * @Given /^I press "([^"]*)"$/
  */
  public function iPress($cssSelector)
  {
    self::$driver->findElement(WebDriverBy::cssSelector($cssSelector))->click();
  }

  /**
  * @Then /^I should see "([^"]*)" say "([^"]*)"$/
  */
  public function iShouldSeeSay($cssSelector, $expectedText)
  {
    self::$driver->manage()->timeouts()->implicitlyWait(10);
    $elementText = self::$driver->findElement(WebDriverBy::cssSelector($cssSelector))->getText();
    assert($elementText == $expectedText);
    
  }

}