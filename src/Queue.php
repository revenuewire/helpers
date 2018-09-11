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
class Queue
{
    /** @var $sqsClient SqsClient */
    public static $sqsClient;
    public static $queueUrl;
    /**
     * Init
     *
     * @param $region
     * @param $queueUrl
     */
    public static function init($region, $queueUrl)
    {
        self::$sqsClient = new SqsClient([
            "region" => $region,
            "version" => "2012-11-05",
        ]);
        self::$queueUrl = $queueUrl;
    }

    /**
     * fetchItemFromQueue
     *
     * @return mixed|null
     */
    public static function fetchItemFromQueue($numberOfItems = 10)
    {
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
     */
    public static function deleteBatchMessages($processedItem)
    {
        self::$sqsClient->deleteMessageBatch([
            "QueueUrl" => self::$queueUrl,
            'Entries' => $processedItem
        ]);
    }
}