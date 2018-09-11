<?php
declare(strict_types=1);

namespace RW\Payments\Helpers;
use Aws\S3\S3Client;
use Slim\Http\UploadedFile;

class S3Uploader
{
    /** @var $client S3Client */
    public static $client;
    public static $bucket;

    /**
     * @param $region
     */
    public static function init(string $region, string $bucket)
    {
        self::$client = new S3Client([
            "region" => $region,
            "version" => "2006-03-01"
        ]);
        self::$client->registerStreamWrapper();
        self::$bucket = $bucket;
    }

    /**
     * Upload from Slim Uploaded files
     * @param string $id
     * @param UploadedFile $file
     * @return array
     */
    public static function upload(string $id, UploadedFile $file) :array
    {
        $key = $id . "/" . $file->getClientFilename();
        $file->moveTo("s3://" . self::$bucket . "/" . $key);
        return [ "bucket" => self::$bucket, "key" => $key ];
    }

    /**
     * Upload file from system file path
     * @param string $id
     * @param string $file
     * @return array
     * @throws \Exception
     */
    public static function uploadByFile(string $id, string $file) :array
    {
        $key = $id . "/" . basename($file);
        if (!file_exists($file)) {
            throw new \Exception("No such file");
        }
        if (file_put_contents("s3://" . self::$bucket . "/" . $key, file_get_contents($file))) {
            return [ "bucket" => self::$bucket, "key" => $key ];
        }

        throw new \Exception("Fail to upload the file");
    }

    /**
     * getPresignedURL
     *
     * @param string $bucket
     * @param string $key
     * @param int $ttl
     * @return string
     */
    public static function getPresignedURL(string $bucket, string $key, int $ttl = 5) : string
    {
        $cmd = self::$client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $key
        ]);

        $request =  self::$client->createPresignedRequest($cmd, "+$ttl minutes");

        $presignedUrl = (string) $request->getUri();

        return $presignedUrl;
    }

}