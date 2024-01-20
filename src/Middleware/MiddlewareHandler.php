<?php

namespace Stag\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareHandler implements RequestHandlerInterface
{
  private $middleware = [];
  private $defaultResponse;
  private $container;

  public function __construct(ResponseInterface $response,  ContainerInterface $container, array $middleware = [])
  {
    $this->defaultResponse = $response;
    $this->container = $container;
    $this->middleware = $middleware;
  }

  public function add(MiddlewareInterface $middleware)
  {
    $this->middleware[] = $middleware;
  }

  public function handle(ServerRequestInterface $request): ResponseInterface
  {

    if (empty($this->middleware)) {
      return $this->defaultResponse;
    }

    $middleware = array_shift($this->middleware);

    $middlewareInstance = $this->container->get($middleware);
    return $middlewareInstance->process($request, $this);
  }
}
