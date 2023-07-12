<?php
use App\Controllers\ImageController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
require_once __DIR__ . '/../src/Controllers/ImageController.php';
require_once __DIR__ . '/../src/Services/ImageProcessor.php';

class ImageControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $controller = new ImageController();

        $response = $controller->index();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('text/html', $response->headers->get('content-type'));
    }

    public function testCropImage(): void
    {
        $imageProcessorMock = $this->createMock(ImageProcessor::class);
        $imageProcessorMock->expects($this->once())
            ->method('processImage')
            ->willReturn('processed_image_contents');

        $controller = new ImageController($imageProcessorMock);

        $parameters = [
            'image' => 'quiz-example.jpg',
            'params' => [
                'crop-width' => 250,
                'crop-height' => 640,
                'width' => 500,
                'height' => 500,
            ],
        ];

        $response = $controller->cropImage($parameters);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY, $response->getStatusCode());
        $this->assertEquals('processed_image_contents', $response->getTargetUrl());
    }
}
