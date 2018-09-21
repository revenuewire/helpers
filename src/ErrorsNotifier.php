<?php
namespace RW\Helpers;
use Aws\Sns\SnsClient;

/**
 * Class ErrorsNotifier
 */
class ErrorsNotifier
{
    public static $subject;
    public static $topic;
    public static $region;
    /** @var $snsClient SnsClient */
    public static $snsClient;

    /**
     * init
     *
     * @param string $region
     * @param string $topic
     * @param string $subject
     */
    public static function init($region, $topic, $subject = "")
    {
        self::$region = $region;
        self::$subject = $subject;
        self::$topic = $topic;

        self::$snsClient = new SnsClient([
            "region" => $region,
            "version" => "2010-03-31"
        ]);
    }

    /**
     * notify
     *
     * @param $exception
     * @param null $subject
     * @param null $topic
     */
    public static function notify($exception, $subject = null, $topic = null, $env = null)
    {
        if (self::$snsClient === null) {
            return;
        }

        if ((empty($subject) && empty(self::$subject))
                || (empty($topic) && empty(self::$topic))){
            return;
        }

        self::$snsClient->publish([
            "Subject" => empty($subject) ? self::$subject : $subject,
            "TopicArn" => empty($topic) ? self::$topic : $topic,
            "Message" => json_encode([
                "env" => $env,
                "message" => $exception->getMessage(),
                "trace" => $exception->getTraceAsString()
            ])
        ]);
    }
}