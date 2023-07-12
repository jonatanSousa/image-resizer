<?php

namespace App\Controllers;

class ImageProcessor
{
    /**
     * @param $cropWidth
     * @param $cropHeight
     * @param $resizeWidth
     * @param $resizeHeight
     * @param $sourceImage
     * @param $outputFunction
     * @return false|string
     */
    public function processImage(
        $cropWidth, $cropHeight, $resizeWidth, $resizeHeight, $sourceImage, $outputFunction
    ) {
        // Perform cropping if crop dimensions are provided
        if ($cropWidth && $cropHeight) {
            // Create a new image with the desired crop size
            $croppedImage = imagecreatetruecolor($cropWidth, $cropHeight);

            // Perform the crop
            imagecopy($croppedImage, $sourceImage, 0, 0, 0, 0, $cropWidth, $cropHeight);

            // Set the cropped image as the new source image
            $sourceImage = $croppedImage;
        }

        // Perform resizing if resize dimensions are provided
        if ($resizeWidth && $resizeHeight) {
            // Create a new image with the desired resize size
            $resizedImage = imagecreatetruecolor($resizeWidth, $resizeHeight);

            // Perform the resize
            imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $resizeWidth, $resizeHeight, imagesx($sourceImage), imagesy($sourceImage));

            // Set the resized image as the new source image
            $sourceImage = $resizedImage;
        }

        // Create a temporary buffer to store the image
        ob_start();

        // Output the image to the buffer using the appropriate image*() function based on the original format
        $outputFunction($sourceImage, null);

        // Get the contents of the buffer
        $imageContents = ob_get_clean();

        // Clean up the image resources
        imagedestroy($sourceImage);
        if (isset($croppedImage)) {
            imagedestroy($croppedImage);
        }
        if (isset($resizedImage)) {
            imagedestroy($resizedImage);
        }

        return $imageContents;
    }

    /**
     * @param resource $image
     * @param int $width
     * @param int $height
     * @return resource
     */
    private function resizeImage($image, int $width, int $height)
    {
        $resizedImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));

        return $resizedImage;
    }

    /**
     * @param resource $image
     * @param int $width
     * @param int $height
     * @return resource
     */
    private function cropImage($image, int $width, int $height)
    {
        $croppedImage = imagecreatetruecolor($width, $height);
        imagecopy($croppedImage, $image, 0, 0, 0, 0, $width, $height);

        return $croppedImage;
    }
}