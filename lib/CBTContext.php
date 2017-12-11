<?php
require 'vendor/autoload.php';

class CBTContext extends Behat\Behat\Context\BehatContext
{
    protected $CONFIG;
    protected static $driver;
    protected static $url;
    private static $api = null;

    public function __construct($parameters)
    {
        $GLOBALS['CONFIG'] = $parameters["cbt"];

        $GLOBALS['CBT_USERNAME'] = getenv('CBT_USERNAME');
        if (!$GLOBALS['CBT_USERNAME'])
            $GLOBALS['CBT_USERNAME'] = $GLOBALS['CONFIG']['user'];

        $GLOBALS['CBT_AUTHKEY'] = getenv('CBT_AUTHKEY');
        if (!$GLOBALS['CBT_AUTHKEY'])
            $GLOBALS['CBT_AUTHKEY'] = $GLOBALS['CONFIG']['key'];

        $GLOBALS['CBT_URL'] = getenv('CBT_URL');
        if (!$GLOBALS['CBT_URL'])
            $GLOBALS['CBT_URL'] = $GLOBALS['CONFIG']['base_url'];


        self::$api = new Drewberry\Api($parameters['cbt']['user'], $parameters['cbt']['key']);
    }

    /** @BeforeFeature */
    public static function setup()
    {
        $CONFIG = $GLOBALS["CONFIG"];

        # Each parallel test we are running will contain
        $test_run_id = getenv("TEST_RUN_ID") ? getenv("TEST_RUN_ID") : 0;

        # build the webdriver hub URL (e.g. https://username:authkey@crossbrowsertesting.com:80/wd/hub)
        $url = "https://".$GLOBALS["CBT_USERNAME"].":".$GLOBALS["CBT_AUTHKEY"]."@".$CONFIG["server"]."/wd/hub";

        # get the capabilities for this test_run_id 
        # caps contains the os, browser, and resolution
        $browserCaps = $CONFIG["browsers"][$test_run_id];
        # pull in capabilities that we want applied to all tests 
        foreach ($CONFIG["capabilities"] as $capName => $capValue) {
            if (!array_key_exists($capName, $browserCaps))
                $browserCaps[$capName] = $capValue;
        }

        self::$driver = RemoteWebDriver::create($url, $browserCaps, 120000, 120000);
        self::$url    = $GLOBALS["CBT_URL"];
    }

    private static function scoreTest($status)
    {
        return self::$api->score(self::$driver->getSessionID(), $status);
    }

    /** @AfterFeature */
    public static function tearDown(Behat\Behat\Event\FeatureEvent $scope)
    {

        /*
         * getResult() - returns the resulting (highest)
         * feature run code:
         * 4 when the feature has failed steps,
         * 3 when the feature has undefined steps,
         * 2 when the feature has pending steps,
         * 0 when all steps are passing.
         */

        if ($scope->getResult() === 0) {
            self::scoreTest('pass');
        } else {
            self::scoreTest('fail');
        }

        self::$driver->quit();
    }
}