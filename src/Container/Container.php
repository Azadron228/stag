<?php

namespace Stag\Container;

use Closure;
use DI\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
  private array $instance = [];

  public function get(string $id)
  {
    if (!$this->has($id)) {
      $this->set($id);
    }

    $object = $this->instance[$id];
    if ($object instanceof Closure) {
      return $object();
    }
    return $object;
  }

  public function resolve($data)
  {
    $reflection = new ReflectionClass($data);
    if (($constructor = $reflection->getConstructor()) === null) {
      return $reflection->newInstance();
    }

    $arguments = $constructor->getParameters();
    $dependencies = $this->getDependencies($arguments);
    return $reflection->newInstanceArgs($dependencies);
  }

  private function getDependencies(array $arguments)
  {
    $dependencies = [];
    foreach ($arguments as $argument) {
      if ($argument->isDefaultValueAvailable()) {
        $dependencies[] = $argument->getDefaultValue();
        continue;
      }
      if ($argument->hasType()) {
        if (($type = $argument->getType()) !== null) {
          if (!$type->isBuiltin()) {
            $dependencies[] = $this->get($type->getName());
            continue;
          }
        }
      }
      if ($argument->allowsNull()) {
        $dependencies[] = null;
        continue;
      }
      throw new NotFoundException('Cannot resolve class dependency ' . $argument->name);
    }
    return $dependencies;
  }


  public function set(string $id, mixed $data = null): void
  {

    if ($data === null) {
      $data = $id;
    }

    if (is_string($data) && class_exists($data)) {
      $data = $this->resolve($data);
    }

   $this->instance[$id] = $data;
  }

  public function has(string $id): bool
  {
    return isset($this->instance[$id]);
  }
}

