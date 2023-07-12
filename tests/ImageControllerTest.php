<?php

use App\Controllers\ImageController;
use App\Services\ImageProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../src/Controllers/ImageController.php';
require_once __DIR__ . '/../src/Services/ImageProcessor.php';

class ImageControllerTest extends TestCase
{
    public function testIndex(): void
    {
        $controller = new ImageController(ImageProcessor::class);

        $response = $controller->index();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('text/html', $response->headers->get('content-type'));
    }

    public function testCropImageWillRedirectToResizedImage(): void
    {
        $imageProcessorMock = $this->createMock(ImageProcessor::class);
        $imageProcessorMock->expects($this->once())
            ->method('processImage')
            ->willReturn('quiz-example_cw_250_cx_640_w_500_h_500.jpg');

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
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals('quiz-example_cw_250_cx_640_w_500_h_500.jpg', $response->getTargetUrl());
    }

    public function testCropImageWillReturnTheImageInTheFS(): void
    {
        $imageProcessorMock = $this->createMock(ImageProcessor::class);
        $imageProcessorMock->expects($this->once())
            ->method('processImage')
            ->willReturn('quiz-koala_cw_250_cx_640_w_500_h_500.jpg');

        $controller = new ImageController($imageProcessorMock);

        $parameters = [
            'image' => 'koala.webp',
            'params' => [
                'crop-width' => 250,
                'crop-height' => 640,
                'width' => 500,
                'height' => 500,
            ],
        ];

        $response = $controller->cropImage($parameters);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('koala_cw_250_cx_640_w_500_h_500.webp', $response->getTargetUrl());
    }
}
