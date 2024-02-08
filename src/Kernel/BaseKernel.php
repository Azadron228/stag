<?php

namespace Stag\Kernel;

require_once __DIR__ . '/../Helpers/Helpers.php';

use Nyholm\Psr7\Response as Psr7Response;
use Stag\Container\Container;
use Stag\DB\Database;
use Stag\Exception\ErrorHandler;
use Stag\Kernel\KernelInterface;
use Stag\Logger\Logger;
use Stag\Middleware\MiddlewareHandler;
use Stag\Routing\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class BaseKernel implements KernelInterface
{
  protected $middleware = [
    // AppMiddleware::class,
  ];

  private Router $router;
  private Logger $logger;
  private Container $container;
  private ErrorHandler $errorHandler;
  private Database $database;

  public function __construct()
  {
    session_start();
    $this->setupLogger();
    $this->setupErrorHandler();
    $this->setupContainer();
    $this->registerServices();
  }

  public function setupLogger()
  {
    $logger = new Logger(APP_ROOT . '/log.txt');
    $this->logger = $logger;
  }

  public function setupContainer(): ContainerInterface
  {
    $config = require_once APP_ROOT . 'config/services.php';

    $this->container = new Container($config);
    return $this->container;
  }

  public function registerServices()
  {
    $this->setupDb();
    $this->setupRoutes();
  }

  public function setupDb()
  {
    $this->container->set(Database::class, function () {
      $config = include APP_ROOT . 'config/database.php';
      return new Database($config);
    });
  }

  public function setupRoutes()
  {
    $this->router = new Router($this->container);
    $routes = require APP_ROOT . 'app/routes/routes.php';
    $routes($this->router);
    return $this->router;
  }

  public function setupErrorHandler()
  {
    $this->errorHandler = new ErrorHandler($this->getLogger());
    $this->errorHandler->register();
  }

  public function getContainer(): ContainerInterface
  {
    return $this->container;
  }

  public function getLogger(): LoggerInterface
  {
    return $this->logger;
  }

  public function handleMiddleware($request)
  {
    $response = new Psr7Response();
    $RequesHandler = new MiddlewareHandler($response, $this->container, $this->middleware);
    $RequesHandler->handle($request);
  }

  public function handleRequest(ServerRequestInterface $request)
  {
    $this->handleMiddleware($request);
    $response = $this->router->matchRoute($request);

    (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
  }
}
