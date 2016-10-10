#!/usr/bin/env php
<?php

require 'vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

include 'cbt-local.php';
use CBT\LocalConnection;

$config_file = getenv('CONFIG_FILE');
if(in_array('-c', $argv)){
    $config_file = $argv[array_search('-c', $argv) + 1];
}
if(!$config_file) $config_file = 'config/single.conf.yml';

$CONFIG = Yaml::parse(file_get_contents($config_file))["default"]["context"]["parameters"]["cbt"];

if(in_array('-l', $argv)){
    print $config['user'] . $CONFIG['key'];
    $local = new LocalConnection($CONFIG['user'], $CONFIG['key']);
    $local->start();
}


$procs = array();

foreach ($CONFIG['browsers'] as $index => $value) {
    // TEST_RUN_ID=0 ./bin/behat --config=single.conf.yml 2>&1
    $cmd = "TEST_RUN_ID=$index ./bin/behat --config=" . $config_file . " 2>&1\n";
    print $value['os_api_name'] . ", " . $value['browser_api_name'] . ", " . $value['resolution'] . "\n";
    $procs[$index] = popen($cmd, "r");
}

foreach ($procs as $key => $value) {
    while (!feof($value)) { 
        print fgets($value);
    }
    pclose($value);
}
if($local){
    $local->stop();
}

?>
