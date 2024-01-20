<?php

namespace Stag\Exception;

use ErrorException;
use Psr\Log\LoggerInterface;

class ErrorHandler
{

  protected LoggerInterface $logger;

  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  public function handleErrors($severity, $message, $file, $line)
  {
    $this->logger->error('An error occurred', ['error' => $message]);

    var_dump($severity);
    var_dump($message);
    var_dump($file);
    var_dump($line);

    throw new ErrorException($message, 0, $severity, $file, $line);
  }

  public function handleExceptions(\Throwable $exception)
  {
    $this->logger->error('An exception occurred', ['exception' => $exception]);

    $statusCode = 500;
    $responseBody = 'Internal Server Error';

    header("HTTP/1.1 $statusCode");
    echo $exception;
    echo $responseBody;
    return;
  }

  public function register()
  {
    set_error_handler([$this, 'handleErrors']);
    set_exception_handler([$this, 'handleExceptions']);
  }
}
