<?php declare(strict_types = 1);

require_once '../vendor/autoload.php';

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use App\Services\ImageProcessor;

try {
    // Load routes from the yaml file
    $fileLocator = new FileLocator(array(__DIR__));
    $loader = new YamlFileLoader($fileLocator);
    $routes = $loader->load('routes.yaml');
    // Init RequestContext object
    $context = new RequestContext();
    $context->fromRequest(Request::createFromGlobals());
    // Init UrlMatcher object
    $matcher = new UrlMatcher($routes, $context);
    // Find the current route
    $parameters = $matcher->match($context->getPathInfo());

    $request = Request::createFromGlobals();

    // Access query parameters
    $parameters['params'] = $request->query->all();

    // Get controller and method from the route parameters
    [$controllerClass, $method] = explode('::', $parameters['_controller']);
    // Create an instance of the controller
    $controller = new $controllerClass(new ImageProcessor);

    // Call the specified method with parameters
    $response = $controller->$method($parameters);
    // Handle the response as needed
    $response->send();

} catch (ResourceNotFoundException $e) {
    echo $e->getMessage();
}