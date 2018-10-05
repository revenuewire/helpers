<?php
namespace RW\Helpers;
use Aws\S3\S3Client;

class S3Uploader
{
    /** @var $client S3Client */
    public static $client;
    public static $bucket;

    /**
     * init
     *
     * @param $region
     * @param $bucket
     */
    public static function init($region, $bucket)
    {
        self::$client = new S3Client([
            "region" => $region,
            "version" => "2006-03-01"
        ]);
        self::$client->registerStreamWrapper();
        self::$bucket = $bucket;
    }

    /**
     * Upload file from system file path
     * @param string $id
     * @param string $file
     * @return array
     * @throws \Exception
     */
    public static function uploadByFile($id, $file, $bucket = null)
    {
        if ($bucket === null) {
            $bucket = self::$bucket;
        }

        $key = $id . "/" . basename($file);
        if (!file_exists($file)) {
            throw new \Exception("No such file");
        }
        if (file_put_contents("s3://" . $bucket . "/" . $key, file_get_contents($file))) {
            return [ "bucket" => $bucket, "key" => $key ];
        }

        throw new \Exception("Fail to upload the file");
    }

    /**
     * uploadByStream
     *
     * @param $key
     * @param $body
     * @param null $bucket
     * @return \Aws\Result|null
     */
    public static function uploadByStream($key, $body, $bucket = null)
    {
        if (self::$client === null) {
            return null;
        }

        if ($bucket === null) {
            $bucket = self::$bucket;
        }

        $requestParam = [
            'Bucket' => $bucket,
            'Key'    => $key,
            'Body' => $body
        ];

        return self::$client->putObject($requestParam);
    }

    /**
     * @param $key
     * @param null $filename
     * @param null $bucket
     * @param int $ttl
     * @return null|string
     */
    public static function getPresignedURL($key, $filename = null, $bucket = null, $ttl = 5)
    {
        if (self::$client === null) {
            return null;
        }

        if ($bucket === null) {
            $bucket = self::$bucket;
        }
        $requestParam = [
            'Bucket' => $bucket,
            'Key'    => $key
        ];
        if ($filename !== null) {
            $requestParam["ResponseContentDisposition"] = "attachment; filename=$filename";
        }

        $cmd = self::$client->getCommand('GetObject', $requestParam);

        $request =  self::$client->createPresignedRequest($cmd, "+$ttl minutes");

        $presignedUrl = (string) $request->getUri();

        return $presignedUrl;
    }

}