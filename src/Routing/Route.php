<?php

namespace Stag\Routing;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Route
{

  private string $uri;
  private string $method;
  private $action;
  private $middleware;
  private $name;
  private array $params = [];
  private ContainerInterface $container;

  public function __construct($uri, $method, $action, $middleware = [], $name = null, ContainerInterface $container)
  {
    $this->uri = $uri;
    $this->method = $method;
    $this->action = $action;
    $this->middleware = $middleware;
    $this->name = $name;
    $this->container = $container;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
  {
    $params = $this->getParams();
    return $this->executeClosure($this, $params);
  }

  public function executeClosure($route, array $params)
  {
    $action = $route->getAction();

    if ($action instanceof \Closure) {
      return $this->executeClosureAction($action, $params);
    }

    return $this->executeControllerAction($route, $params);
  }

  private function executeClosureAction(\Closure $action, $params)
  {
    $reflectionFunction = new \ReflectionFunction($action);
    $dependencies = [];

    foreach ($reflectionFunction->getParameters() as $parameter) {
      $parameterType = $parameter->getType();
      if ($parameterType !== null && !$parameterType->isBuiltin()) {
        $className = $parameterType->getName();
        $dependencies[] = $this->container->get($className);
      }
    }

    if (count($params) > 0) {
      $dependencies = array_merge($dependencies, $params);
    }

    return $action(...$dependencies);
  }

  private function executeControllerAction($route, $params)
  {
    $controller = $this->container->get($route->getAction()[0]);
    $action = $route->getAction()[1];

    $reflectionMethod = new \ReflectionMethod($controller, $action);
    $dependencies = [];

    foreach ($reflectionMethod->getParameters() as $parameter) {
      $parameterType = $parameter->getType();
      if ($parameterType !== null && !$parameterType->isBuiltin()) {
        $className = $parameterType->getName();
        $dependencies[] = $this->container->get($className);
      }
    }

    if (!isset($params)) {
      return $controller->{$action}(...$dependencies);
    } else {
      return $controller->{$action}(...$dependencies, ...$params);
    }
  }

  public function middleware(array $middleware)
  {
    $this->middleware = $middleware;
    return $this;
  }

  public function getParams(): array
  {
    return $this->params;
  }

  public function setParams($params)
  {
    $this->params = $params;
  }

  public function getUri()
  {
    return $this->uri;
  }

  public function getMethod()
  {
    return $this->method;
  }

  public function getAction()
  {
    return $this->action;
  }

  public function getMiddleware()
  {
    return $this->middleware;
  }

  public function getName()
  {
    return $this->name;
  }
}
