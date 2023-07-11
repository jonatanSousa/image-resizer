<?php
namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;

class ImageController
{

    public function __construct()
    { }
    /**
     * @Route("/display",name="index")
     */
    public function index(): Response
    {
        return new Response("This is index method", Response::HTTP_OK,
            ['content-type' => 'text/plain']);
    }

    public function cropImage($parameters): Response
    {
        // Path to the original image
        $originalImagePath = '../images/' . $parameters['image'];

        $extension = pathinfo($originalImagePath, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $sourceImage = imagecreatefromjpeg($originalImagePath);
                $outputFunction = 'imagejpeg';
                break;
            case 'png':
                $sourceImage = imagecreatefrompng($originalImagePath);
                $outputFunction = 'imagepng';
                break;
            case 'gif':
                $sourceImage = imagecreatefromgif($originalImagePath);
                $outputFunction = 'imagegif';
                break;
            case 'webp':
                $sourceImage = imagecreatefromwebp($originalImagePath);
                $outputFunction = 'imagewebp';
                break;
            default:
                // Handle unsupported image format
                die('Unsupported image format.');
        }

        // Get crop dimensions
        $cropWidth = $parameters['params']['crop-width'] ?? null;
        $cropHeight = $parameters['params']['crop-height'] ?? null;

        // Get resize dimensions
        $resizeWidth = $parameters['params']['width'] ?? null;
        $resizeHeight = $parameters['params']['height'] ?? null;

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

        /*$originalImagePath = '../images/' . $parameters['image'];
        // Redirect to the URL without query parameters
        return new RedirectResponse($originalImagePath);*/

        return new Response($imageContents, Response::HTTP_OK, [
            'Content-Type' => 'image/' . $extension,
            'Content-Disposition' => 'inline; filename=image.' . $extension,
        ]);
    }
}