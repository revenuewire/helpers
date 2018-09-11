<?php
/**
 * Created by IntelliJ IDEA.
 * User: swang
 * Date: 2018-01-24
 * Time: 3:41 PM
 */

namespace RW\Helpers;

use Aws\Sns\SnsClient;

/**
 * Class Event
 * @package RW\PayAPI\Helpers
 * @codeCoverageIgnore
 */
class Event
{
    /** @var $snsClient SnsClient */
    public static $snsClient;
    public static $topic;

    /**
     * Init
     *
     * @param $region
     * @param $topic
     */
    public static function init($region, $topic)
    {
        self::$snsClient = new SnsClient([
            "region" => $region,
            "version" => "2010-03-31"
        ]);
        self::$topic = $topic;
    }

    /**
     * Push event
     *
     * @param $subject
     * @param $payload
     */
    public static function push($subject, $payload)
    {
        self::$snsClient->publish([
            "Subject" => $subject,
            "TopicArn" => self::$topic,
            "Message" => json_encode($payload)
        ]);
    }
}