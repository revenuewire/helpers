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
     * getId
     * @return string
     */
    public static function getId()
    {
        return date("Y-m-d") . "-" . IdFactory::generate(12);
    }

    /**
     * Upload file from system file path
     * @param string $file
     * @return array
     * @throws \Exception
     */
    public static function uploadByFile($file)
    {
        if (self::$client === null) {
            return null;
        }

        $id = self::getId();
        $key = $id . "/" . basename($file);
        if (!file_exists($file)) {
            throw new \Exception("No such file");
        }
        if (file_put_contents("s3://" . self::$bucket . "/" . $key, file_get_contents($file))) {
            return [ "bucket" => self::$bucket, "key" => $key, "filename" => basename($file) ];
        }

        throw new \Exception("Fail to upload the file");
    }

    /**
     * uploadByStream
     *
     * @param $name
     * @param $body
     * @return array|null
     */
    public static function uploadByStream($name, $body)
    {
        if (self::$client === null) {
            return null;
        }

        $id = self::getId();
        $key = $id . "/" . $name;

        $requestParam = [
            'Bucket' => self::$bucket,
            'Key'    => $key,
            'Body' => $body
        ];

        self::$client->putObject($requestParam);

        return [ "bucket" => self::$bucket, "key" => $key , "filename" => $name];
    }

    /**
     * getPresignedURL
     *
     * @param $key
     * @param null $filename
     * @param int $ttl
     * @return null|string
     */
    public static function getPresignedURL($key, $filename = null, $ttl = 5)
    {
        if (self::$client === null) {
            return null;
        }

        $requestParam = [
            'Bucket' => self::$bucket,
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