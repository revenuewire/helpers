<?php
/**
 * Created by IntelliJ IDEA.
 * User: swang
 * Date: 2018-04-30
 * Time: 3:15 PM
 */

namespace RW\Helpers;

use Aws\Sqs\SqsClient;

/**
 * Class Queue
 * @package RW\Payments\Helpers
 * @codeCoverageIgnore
 */
class AuditLog
{
    /** @var $sqsClient SqsClient */
    public static $sqsClient;
    public static $queueUrl;
    public static $service;

    /**
     * Init
     *
     * @param $region
     * @param $queueUrl
     * @param $namespace
     */
    public static function init(string $region, string $queueUrl, string $service)
    {
        self::$sqsClient = new SqsClient([
            "region" => $region,
            "version" => "2012-11-05",
        ]);
        self::$queueUrl = $queueUrl;
        self::$service = $service;
    }

    /**
     * @param string $reference
     * @param string $event
     * @param string $clientId
     * @param string $user
     * @param array $context
     */
    public static function addLog(string $reference, string $event, string $clientId, string $user = "", array $context = [])
    {
        $eventItem = [
            "reference" => $reference,
            "event" => $event,
            "user" => $user,
            "clientId" => $clientId,
            "created" => time(),
            "context" => $context
        ];
        self::placeItemIntoQueue(self::$service, json_encode($eventItem));
    }

    /**
     * fetchItemFromQueue
     *
     * @return mixed|null
     */
    public static function fetchItemFromQueue($numberOfItems = 10)
    {
        if (self::$sqsClient === null) {
            return null;
        }
        $result = self::$sqsClient->receiveMessage([
            "QueueUrl" => self::$queueUrl,
            "MaxNumberOfMessages" => $numberOfItems,
        ]);

        return $result->get('Messages');
    }

    /**
     * placeItemIntoQueue
     * @param string $subject
     * @param array $message
     * @param int $delaySeconds
     * @return array
     */
    public static function placeItemIntoQueue($subject, $message, $delaySeconds = 0)
    {
        if (self::$sqsClient === null) {
            return null;
        }

        return self::$sqsClient->sendMessage([
            'DelaySeconds' => $delaySeconds,
            "QueueUrl" => self::$queueUrl,
            'MessageBody' => json_encode([
                "Subject" => $subject,
                "Message" => $message,
            ])
        ])->toArray();
    }

    /**
     * Delete batch messages
     *
     * @param array $processedItem
     * @return null
     */
    public static function deleteBatchMessages($processedItem)
    {
        if (self::$sqsClient === null) {
            return null;
        }

        self::$sqsClient->deleteMessageBatch([
            "QueueUrl" => self::$queueUrl,
            'Entries' => $processedItem
        ]);
    }
}