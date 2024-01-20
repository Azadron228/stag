<?php

namespace Stag\Routing;

trait RouterRequestMethodsTrait
{
  
  public function addRoute(string $uri, string $requestMethod, array|callable $target, string $name = null, array $middleware = [])
  {
    $route = new Route($uri, $requestMethod, $target, $name, $middleware);
    $this->routes[] = $route;
    return $route;
  }

  public function get($uri, $target)
  {
    return $this->addRoute($uri, 'GET', $target);
  }

  public function post($uri, $target)
  {
    return $this->addRoute($uri, 'POST', $target);
  }

  public function put($uri, $target)
  {
    return $this->addRoute($uri, 'PUT', $target);
  }

  public function patch($uri, $target)
  {
    return $this->addRoute($uri, 'PATCH', $target);
  }

  public function delete($uri, $target)
  {
    return $this->addRoute($uri, 'DELETE', $target);
  }

  public function any($uri, $target)
  {
    return $this->addRoute($uri, 'GET|POST|PUT|PATCH|DELETE', $target);
  }
}
