<?php

require 'vendor/autoload.php';

class CBTContext extends Behat\Behat\Context\BehatContext
{
    protected $CONFIG;
    protected static $driver;

    public function __construct($parameters){
        $GLOBALS['CONFIG'] = $parameters["cbt"];

        $GLOBALS['CBT_USERNAME'] = getenv('CBT_USERNAME');
        if(!$GLOBALS['CBT_USERNAME']) $GLOBALS['CBT_USERNAME'] = $GLOBALS['CONFIG']['user'];

        $GLOBALS['CBT_AUTHKEY'] = getenv('CBT_AUTHKEY');
        if(!$GLOBALS['CBT_AUTHKEY']) $GLOBALS['CBT_AUTHKEY'] = $GLOBALS['CONFIG']['key'];
    }

    /** @BeforeFeature */
    public static function setup()
    {
        $CONFIG = $GLOBALS["CONFIG"]; 

        # Each parallel test we are running will contain  
        $test_run_id = getenv("TEST_RUN_ID") ? getenv("TEST_RUN_ID") : 0; 
        
        # build the webdriver hub URL (e.g. https://username:authkey@crossbrowsertesting.com:80/wd/hub)
        $url = "https://" . $GLOBALS["CBT_USERNAME"] . ":" . $GLOBALS["CBT_AUTHKEY"] . "@" . $CONFIG["server"] ."/wd/hub";

        # get the capabilities for this test_run_id 
        # caps contains the os, browser, and resolution
        $browserCaps = $CONFIG["browsers"][$test_run_id];
        # pull in capabilities that we want applied to all tests 
        foreach ($CONFIG["capabilities"] as $capName => $capValue) {
            if(!array_key_exists($capName, $browserCaps))
                $browserCaps[$capName] = $capValue;
        }

        self::$driver = RemoteWebDriver::create($url, $browserCaps, 120000, 120000);
    }

    /** @AfterFeature */
    public static function tearDown()
    {
        self::$driver->quit();
    }
}
?>
