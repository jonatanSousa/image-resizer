<?php
namespace App\Controllers;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ImageController
{
    public function __construct(
        public $imageProcessor  = new ImageProcessor()
    )
    {}

    /**
     * @Route("/display",name="index")
     */
    public function index(): Response
    {
        $html = '
                <h1>Original image resized  </h1> 
                <p><b>Usage :</b> http://localhost:8080/quiz-example.jpg?width=500&height=500 </p>
                <img src="http://localhost:8080/quiz-example.jpg?width=500&height=500">
                <h1>Original image cropped and resized </h1> 
                <p><b>Usage :</b> http://localhost:8080/quiz-example.jpg?crop-width=250&crop-height=640&width=200&height=200 </p>
                <img src="http://localhost:8080/quiz-example.jpg?crop-width=250&crop-height=640&width=500&height=500">
                <h1>Original image just cropped</h1> 
                <p><b>Usage :</b> http://localhost:8080/quiz-example.jpg?crop-width=250&crop-height=640 </p>
                <img src="http://localhost:8080/quiz-example.jpg?crop-width=250&crop-height=640" >
                <h1>Original image no changes </h1> 
                <img src="http://localhost:8080/quiz-example.jpg">
                ';

        return new Response($html, Response::HTTP_OK,
            ['content-type' => 'text/html']);
    }

    /**
     * @param $parameters
     * @return Response
     */
    public function cropImage($parameters): Response
    {
        $originalImagePath = '../images/' . $parameters['image'];
        $fileName = pathinfo($parameters['image'], PATHINFO_FILENAME);
        $extension = pathinfo($originalImagePath, PATHINFO_EXTENSION);

        //get source image
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
                return new Response('Unsupported image format.', Response::HTTP_BAD_REQUEST);
        }

        // Create a temporary buffer to store the image
        ob_start();

        // Output the image to the buffer using the appropriate image*() function based on the original format
        $outputFunction($sourceImage, null);

        // Get the contents of the buffer
        $imageContents = ob_get_clean();

        // Get crop dimensions
        $cropWidth = $parameters['params']['crop-width'] ?? null;
        $cropHeight = $parameters['params']['crop-height'] ?? null;

        // Get resize dimensions
        $resizeWidth = $parameters['params']['width'] ?? null;
        $resizeHeight = $parameters['params']['height'] ?? null;

        //Has file been generated ?
        if(count($parameters['params'])) {
            $fileName .= '_cw_'.$cropWidth."_cx_".$cropHeight."_w_". $resizeWidth."_h_".$resizeHeight;
        }

        if (file_exists('../images/' . $parameters['image']) && !count($parameters['params'])) {
            return new Response($imageContents, Response::HTTP_OK, [
                'Content-Type' => 'image/' . $extension,
                'Content-Disposition' => 'inline; filename=image.' . $extension,
            ]);
        }
        // Get the contents of the buffer
        try {
            $imageContents = $this->imageProcessor->processImage(
                $cropWidth,
                $cropHeight,
                $resizeWidth,
                $resizeHeight,
                $sourceImage,
                $outputFunction
            );
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        //savefile generated image to be used later
        file_put_contents('../images/' . $fileName.'.'.$extension, $imageContents);

        return new RedirectResponse($fileName.'.'.$extension,Response::HTTP_MOVED_PERMANENTLY);
    }
}