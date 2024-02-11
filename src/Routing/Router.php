<?php

namespace Stag\Routing;

use Nyholm\Psr7\Response;
use Stag\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router implements RequestHandlerInterface
{
  use RouterRequestMethodsTrait;

  protected array $middleware = [];
  protected array $routes = [];
  protected Container $container;

  protected Route $route;

  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  public function handle(ServerRequestInterface $request): ResponseInterface
  {
    array_push($this->middleware, $this->route);

    if (empty($this->middleware)) {
      return response();
    }

    $middlewareInstance = array_shift($this->middleware);
    if (is_string($middlewareInstance)) {
      $middlewareInstance = $this->container->get($middlewareInstance);
    }

    $response = $middlewareInstance->process($request, $this);

    if ($response !== null) {
      return $response;
    }

    if (!$this->route) {
      return response()->withStatus(404);
    }

    return response();
  }

  public function dispatch(ServerRequestInterface $request): ResponseInterface
  {
    $route = $this->matchRoute($request);
    if ($route == null) {return response()->withStatus(404);}

    $this->route = $route;
    $middlewares = $route->getMiddleware();

    if($middlewares !== null){$this->middleware = $middlewares;}


    $response = $this->handle($request);

    if($response == null){
      return new Response(); 
    }

    return $response;
  }


  public function parseRoute($route, $requestUri)
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

  public function matchRoute(ServerRequestInterface $request): Route
  {
    $requestMethod = $request->getMethod();
    $requestUri = $request->getUri();
    $parsedUri = explode('/', trim(parse_url($requestUri)["path"], '/'));

    foreach ($this->routes as $route) {
      if ($route->getMethod() === $requestMethod) {
        $pattern = $this->parseRoute($route, $parsedUri);

        $uri = $pattern[0];
        $params = $pattern[1];

        if ($parsedUri === $uri) {
          $route->setParams($params);
          return $route;
        }
      }
    }

    return null;
  }
}
