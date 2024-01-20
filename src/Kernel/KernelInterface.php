<?php

namespace Stag\Kernel;

use Psr\Container\ContainerInterface;

interface KernelInterface
{
  public function setupDb();
  public function setupRoutes();

  public function setupContainer(): ContainerInterface;
  public function getContainer(): ContainerInterface;

}
