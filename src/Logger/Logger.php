<?php

namespace Stag\Logger;

use Psr\Log\LoggerInterface;
use Stringable;

class Logger implements LoggerInterface
{
  public const DEBUG = 7;
  public const INFO = 6;
  public const NOTICE = 5;
  public const WARNING = 4;
  public const ERROR = 3;
  public const CRITICAL = 2;
  public const ALERT = 1;
  public const EMERGENCY = 0;

  private const levels = [
    7 => 'Debug',
    6 => 'Info',
    5 => 'Notice',
    4 => 'Warning',
    3 => 'Error',
    2 => 'Critical',
    1 => 'Alert',
    0 => 'Emergency',
  ];

  private $logFilePath;

  public function __construct($logFilePath)
  {
    $this->logFilePath = $logFilePath;
  }

  public function debug(string|Stringable $message, array $context = []): void
  {
    $this->log(self::DEBUG, $message, $context);
  }

  public function alert(string|Stringable $message, array $context = []): void
  {
  }

  public function critical(string|Stringable $message, array $context = []): void
  {
  }

  public function warning(string|Stringable $message, array $context = []): void
  {
  }

  public function info(string|Stringable $message, array $context = []): void
  {
    $this->log(self::DEBUG, $message, $context);
  }
  public function notice(string|Stringable $message, array $context = []): void
  {
    $this->log(self::DEBUG, $message, $context);
  }
  public function error(string|Stringable $message, array $context = []): void
  {
    $this->log(self::ERROR, $message, $context);
  }
  public function emergency(string|Stringable $message, array $context = []): void
  {
    $this->log(self::EMERGENCY, $message, $context);
  }

  public function writer($message, $levelName)
  {
    file_put_contents($this->logFilePath, "[$levelName] $message" . PHP_EOL, FILE_APPEND);
  }

  public function log($level, string|Stringable $message, array $context = []): void
  {
    $levelName = self::levels[$level];

    $message = $this->interpolate($message, $context);

    $this->writer($message, $levelName);
  }

  /**
   * Interpolates context values into the message placeholders.
   */
  function interpolate($message, array $context = array())
  {
    // build a replacement array with braces around the context keys
    $replace = array();
    foreach ($context as $key => $val) {
      // check that the value can be cast to string
      if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
        $replace['{' . $key . '}'] = $val;
      }
    }

    // interpolate replacement values into the message and return
    return strtr($message, $replace);
  }
}
