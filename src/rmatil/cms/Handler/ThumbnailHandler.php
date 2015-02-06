<?php

namespace rmatil\cms\Handler;

use rmatil\cms\Entities\File;
use rmatil\cms\Exceptions\ThumbnailCreationFailedException;
use rmatil\cms\Exceptions\ExtensionNotLoadedException;
use InvalidArgumentException;

class ThumbnailHandler {

    /**
     * Creates a thumbnail either with imagick, if supported, or with 
     * gd library.
     *
     * @param  File    $fileObject           FileObject to add properties
     * @param  string  $httpPathToMediaDir   Http path to file
     * @param  string  $pathToMediaDirectory Local path to file
     * @param  string  $fileName             Name of the image
     * @param  string  $fileExtension        Fileextension
     * @param  integer $width                Width of thumbnail, if omitted, size will be proportional to height
     * @param  integer $height               Height of thumbnail, if omitted, size will be proportional to width
     *
     * @throws ThumbnailCreationFailedException             If creation fails on both libraries
     * @throws \InvalidArgumentException     If parameters are not correct
     */
    public static function createThumbnail(File &$fileObject, $httpPathToMediaDir, $pathToMediaDirectory, $fileName, $fileExtension, $width = null, $height = null) {
        if (!$httpPathToMediaDir ||
            !$pathToMediaDirectory ||
            !$fileName ||
            !$fileExtension ||
            (!$width && !$height)) {
            throw new InvalidArgumentException('Provided parameters are not valid'.sprintf('httpPathToMediaDir:%s, pathToMediaDirectory:%s, fileName:%s, fileExtension:%s, width:%s, height:%s', $httpPathToMediaDir, $pathToMediaDirectory, $fileName, $fileExtension, $width, $height));
        }

        $fileExtension = strtolower($fileExtension);

        try {
            self::createThumbnailWithImagick($fileObject, $httpPathToMediaDir, $pathToMediaDirectory, $fileName, $fileExtension, $width, $height);            
        } catch (ExtensionNotLoadedException $enle) {
            // imagick is not available
            self::createThumbnailWithGd($fileObject, $httpPathToMediaDir, $pathToMediaDirectory, $fileName, $fileExtension, $width, $height);
        }
    }

    /**
     * Creates a thumbnail with imagick.
     *
     * @param  File    $fileObject           FileObject to add properties
     * @param  string  $httpPathToMediaDir   Http path to file
     * @param  string  $pathToMediaDirectory Local path to file
     * @param  string  $fileName             Name of the image
     * @param  string  $fileExtension        Fileextension
     * @param  integer $width                Width of thumbnail, if omitted, size will be proportional to height
     * @param  integer $height               Height of thumbnail, if omitted, size will be proportional to width
     *
     * @throws ThumbnailCreationFailedException              If imagick is not supported
     * @throws InvalidArgumentException      If both, height and width are omitted or file format is not supported
     */
    private static function createThumbnailWithImagick(File &$fileObject, $httpPathToMediaDir, $pathToMediaDirectory, $fileName, $fileExtension, $width = null, $height = null) {
        if (!extension_loaded('imagick')) {
            throw new ExtensionNotLoadedException('Imagick is not loaded on this system');
        }

        if ($width === null && $height === null) {
            throw new InvalidArgumentException('Either width or height must be provided');
        }

        // create thumbnails with imagick
        $imagick = new \Imagick();

        if (!in_array($fileExtension, $imagick->queryFormats("*"))) {
            throw new ThumbnailCreationFailedException('No thumbnail could be created for the file format');
        }

        // read image into imagick
        $imagick->readImage(sprintf('%s/%s.%s', $pathToMediaDirectory, $fileName, $fileExtension));
        // set size
        $imagick->thumbnailImage($width, $height); // null values allowed
        // write image 
        $imagick->writeImage(sprintf('%s/%sx%s-thumbnail-%s.%s', $pathToMediaDirectory, $imagick->getImageWidth(), $imagick->getImageHeight(), $fileName, $fileExtension));

        $fileObject->setDimensions(sprintf('%sx%s', $imagick->getImageWidth(), $imagick->getImageHeight()));
        $fileObject->setThumbnailLink(sprintf('%s/%sx%s-thumbnail-%s.%s', $httpPathToMediaDir, $imagick->getImageWidth(), $imagick->getImageHeight(), $fileName, $fileExtension));
        $fileObject->setLocalThumbnailPath(sprintf('%s/%sx%s-thumbnail-%s.%s', $pathToMediaDirectory, $imagick->getImageWidth(), $imagick->getImageHeight(), $fileName, $fileExtension));

        // free up associated resources
        $imagick->destroy();
    }

    /**
     * Creates a Thumbnail if file type is supported.
     * Supported types are: JPEG, PNG, GIF, WBMP, and GD2.
     *
     * @param File    $fileObject           FileObject to add properties
     * @param string  $httpPathToMediaDir   Http path to file
     * @param string  $pathToMediaDirectory LOCAL Path to file (without backslash at the end)
     * @param string  $fileName             Name of the image
     * @param string  $fileExtension        Fileextension 
     * @param integer $width                Width of thumbnail, if omitted, size will be proportional to height
     * @param integer $heigt                Height of thumbnail, if omitted, size will be proportional to width
     *
     * @throws ThumbnailCreationFailedException             If thumbnail creation fails
     * @throws InvalidArgumentException     If both, height and width, are omitted or the file format is not supported
     */
    private static function createThumbnailWithGd(File &$fileObject, $httpPathToMediaDir, $pathToMediaDirectory, $fileName, $fileExtension, $width = null, $height = null) {
        if ($width === null && $height === null) {
            throw new InvalidArgumentException('Either width or height must be provided');
        }

        if ($fileExtension == 'jpg' || $fileExtension == 'jpeg') {
            $img = imagecreatefromjpeg(sprintf('%s/%s.%s', $pathToMediaDirectory, $fileName, $fileExtension));
        } else if ($fileExtension == 'png') {
            $img = imagecreatefrompng(sprintf('%s/%s.%s', $pathToMediaDirectory, $fileName, $fileExtension));
        } else if ($fileExtension == 'gif') {
            $img = imagecreatefromgif(sprintf('%s/%s.%s', $pathToMediaDirectory, $fileName, $fileExtension));
        } else {
            throw new ThumbnailCreationFailedException('No thumbnail could be created for the file format');
        }

        // image is corrupt, in the wrong file type or contains wrong data
        if ($img === false) {
            throw new ThumbnailCreationFailedException('No thumbnail could be created for the file format');
        }

        $imgWidth      = imagesx($img);
        $imgHeight     = imagesy($img);

        $fileObject->setDimensions(sprintf('%sx%s', $imgWidth, $imgHeight));

        // calculate thumbnail dimensions
        if ($width && !$height) {
            $newHeight = floor($imgHeight * ($width / $imgWidth));
            $newWidth  = $width;
        } elseif (!$width && $height) {
            $newHeight = $height;
            $newWidth  = floor($imgWidth * ($height / $imgHeight));
        }

        // create a new temporary image
        $tmpImg = imagecreatetruecolor($newWidth, $newHeight);
        // check resource identifier
        if ($tmpImg === false) {
            throw new ThumbnailCreationFailedException('Thumbnail creation failed on temporary image');
        }

        // copy and resize old image into new image 
        $ret = imagecopyresized($tmpImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $imgWidth, $imgHeight);
        if ($ret === false) {
            throw new ThumbnailCreationFailedException('Thumbnail creation failed on resizing');
        }

        // save thumbnail into a file, set image quality to 100
        $ret = imagejpeg($tmpImg, sprintf('%s/%sx%s-thumbnail-%s.%s', $pathToMediaDirectory, $newWidth, $newHeight, $fileName, $fileExtension), 100);
        if ($ret === false) {
            throw new ThumbnailCreationFailedException('Thumbnail creation failed on saving thumbnail');
        }

        $fileObject->setDimensions(sprintf('%sx%s', $newWidth, $newHeight));
        $fileObject->setThumbnailLink(sprintf('%s/%sx%s-thumbnail-%s.%s', $httpPathToMediaDir, $newWidth, $newHeight, $fileName, $fileExtension));
        $fileObject->setLocalThumbnailPath(sprintf('%s/%sx%s-thumbnail-%s.%s', $pathToMediaDirectory, $newWidth, $newHeight, $fileName, $fileExtension));

        // free memory
        imagedestroy($tmpImg);
    }
}