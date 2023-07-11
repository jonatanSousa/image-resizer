<?php

use PHPUnit\Framework\TestCase;
require __DIR__ . "/../src/Controllers/ImageController.php";

//use App\Controller\ImageController;
use Symfony\Component\HttpFoundation\Response;

class ImageControllerTest extends TestCase
{
    public function testCropImage(): void
    {
        // Create an instance of the ImageController
        $imageController = new ImageController();

        // Define the test parameters
        $parameters = [
            'image' => 'test_image.jpg',
            'params' => [
                'crop-width' => 200,
                'crop-height' => 200,
                'width' => 400,
                'height' => 400,
            ],
        ];

        // Call the cropImage method with the test parameters
        $response = $imageController->cropImage($parameters);

        // Assert that the response is an instance of Symfony Response
        $this->assertInstanceOf(Response::class, $response);

        // Assert the response status code
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        // Assert the response headers
        $expectedHeaders = [
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline; filename=image.jpg',
        ];
        $this->assertSame($expectedHeaders, $response->headers->all());

        // Assert the response content (image contents)
        $this->assertNotEmpty($response->getContent());
    }
}
