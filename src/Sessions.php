<?php
/**
 * Created by PhpStorm.
 * User: swang
 * Date: 2018-10-10
 * Time: 1:38 PM
 */

namespace RW\Helpers;

use Aws\DynamoDb\DynamoDbClient;

class Sessions
{
    /**
     * @param $networkRegion
     * @param $table
     */
    public function init($networkRegion, $table)
    {
        $dynamoDb = new DynamoDbClient(array(
            'region' => $networkRegion,
            "version" => "2012-08-10",
        ));
        $sessionHandler = \Aws\DynamoDb\SessionHandler::fromClient($dynamoDb, array(
            'table_name' => $table,
        ));
        $sessionHandler->register();
    }
}