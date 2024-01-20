<?php

namespace Stag\Routing;

class Route
{
  private string $uri;
  private string $method;
  private $action;
  private $middleware;
  private $name;

  public function __construct($uri, $method, $action, $middleware = [], $name = null)
  {
    $this->uri = $uri;
    $this->method = $method;
    $this->action = $action;
    $this->middleware = $middleware;
    $this->name = $name;
  }

  public function middleware(array $middleware)
  {
    $this->middleware = $middleware;
    return $this;
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
