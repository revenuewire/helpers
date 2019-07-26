<?php

require_once("./vendor/autoload.php");

echo "2 => 3\n";
foreach(\RW\Helpers\Country::$countries as $country) {
    echo "\"{$country['alpha2']}\" => \"{$country['alpha3']}\",\n";
}

echo "3 => 2\n";
foreach(\RW\Helpers\Country::$countries as $country) {
    echo "\"{$country['alpha3']}\" => \"{$country['alpha2']}\",\n";
}

echo "2 => n\n";
foreach(\RW\Helpers\Country::$countries as $country) {
    echo "\"{$country['alpha2']}\" => \"{$country['numeric']}\",\n";
}

echo "2 => na\n";
foreach(\RW\Helpers\Country::$countries as $country) {
    echo "\"{$country['alpha2']}\" => \"{$country['name']}\",\n";
}