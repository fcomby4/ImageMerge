<?php

namespace Jackal\ImageMerge\Model\Format;

use Exception;
use Jackal\ImageMerge\Http\Response\ImageResponse;

/**
 * Class ImageWriter
 * @package Jackal\ImageMerge\Model\Format
 */
class ImageWriter
{
    /**
     * @param $filePathName
     * @throws Exception
     */
    private static function checkPermissions($filePathName)
    {
        $directory = dirname($filePathName);
        if (!is_dir($directory)) {
            if (!mkdir(dirname($filePathName), 0777, true)) {
                throw new Exception(sprintf('Cannot create folder %s', $directory));
            }
        }
        if (!is_writable($directory)) {
            throw new Exception(sprintf('Cannot write into directory: %s', $directory));
        }
    }

    /**
     * @param $resource
     * @param null $filePathName
     * @return bool|ImageResponse
     * @throws Exception
     */
    public static function toPNG($resource, $filePathName = null)
    {
        ob_start();
        imagepng($resource, null, 9);
        $content = ob_get_clean();

        return self::writeFile($content, $filePathName, 'image/png');
    }

    /**
     * @param $resource
     * @param null $filePathName
     * @return bool|ImageResponse
     * @throws Exception
     */
    public static function toJPG($resource, $filePathName=null)
    {
        ob_start();
        imagejpeg($resource, null, 100);
        $content = ob_get_clean();

        return self::writeFile($content, $filePathName, 'image/jpg');
    }

    /**
     * @param $resource
     * @param null $filePathName
     * @return bool|ImageResponse
     * @throws Exception
     */
    public static function toGIF($resource, $filePathName=null)
    {
        ob_start();
        imagegif($resource);
        $content = ob_get_clean();

        return self::writeFile($content, $filePathName, 'image/gif');
    }

    /**
     * @param $resource
     * @param null $filePathName
     * @return bool|ImageResponse
     * @throws Exception
     */
    public static function toWEBP($resource, $filePathName=null)
    {
        ob_start();
        imagewebp($resource);
        $content = ob_get_clean();

        return self::writeFile($content, $filePathName, 'image/webp');
    }

    /**
     * @param $content
     * @param $filePathName
     * @param $contentType
     * @return bool|ImageResponse
     * @throws Exception
     */
    private static function writeFile($content, $filePathName, $contentType)
    {
        if ($filePathName) {
            ImageWriter::checkPermissions($filePathName);
            return file_put_contents($filePathName, $content) == true;
        }

        return new ImageResponse($content, 200, [
            'content-type' => [$contentType]
        ]);
    }
}
