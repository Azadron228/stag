<?php

namespace Stag\Routing;

use Nyholm\Psr7\Response;
use Stag\Container\Container;
use Stag\Middleware\MiddlewareHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
  use RouterRequestMethodsTrait;

  protected array $middleware = [];
  protected array $routes = [];
  protected Container $container;

  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  public function handle($route, $requestUri)
  {
    $routeSegments = explode('/', trim($route->getUri(), '/'));

    $processedUri = [];
    $parameters = [];

    foreach ($routeSegments as $index => $segment) {
      if (preg_match('/{([a-zA-Z0-9_]*)}/', $segment)) {

        if (isset($requestUri[$index])) {
          $processedUri[] = $requestUri[$index];
          $parameters[] = $requestUri[$index];
        }
      } else {
        $processedUri[] = $segment;
      }
    }

    return [$processedUri, $parameters];
  }

  public function matchRoute(ServerRequestInterface $request)
  {
    $requestMethod = $request->getMethod();
    $requestUri = $request->getUri();
    $parsedUri = explode('/', trim(parse_url($requestUri)["path"], '/'));

    foreach ($this->routes as $route) {
      if ($route->getMethod() === $requestMethod) {
        $pattern = $this->handle($route, $parsedUri);

        $uri = $pattern[0];
        $params = $pattern[1];

        if ($parsedUri === $uri) {

          $this->callMiddleware($route, $request);
          $this->executeClosure($route, $params, $request);

          return;
        }
      }
    }

    return (new Response())->withStatus(404);
  }

  public function callMiddleware($route, $request)
  {
    $RequestHandler = new MiddlewareHandler(new Response(), $this->container, $route->getMiddleware() ?? []);
    $RequestHandler->handle($request);
  }

  public function executeClosure($route, $params, $request)
  {
    $action = $route->getAction();

    // If closure is function
    if ($action instanceof \Closure) {
      if (is_array($params)) {
        $action(...$params);
      }
      return;
    }

    $controller = $this->container->get($route->getAction()[0]);
    $action = $route->getAction()[1];

    return $this->callController($params, $action, $controller, $request);
  }

  protected function callController($params, $action, $controller, $request)
  {
    if (!isset($params)) {
      $controller->{$action}($request);
    } else {
      $controller->{$action}($request,...$params);
    }
  }
}
