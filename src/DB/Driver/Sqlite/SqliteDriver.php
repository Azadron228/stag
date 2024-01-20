<?php

namespace Stag\DB\Driver\Sqlite;

use InvalidArgumentException;
use PDO;
use PDOException;
use RuntimeException;

class SqliteDriver
{

  protected array $config;

  public function __construct(array $config)
  {
    $this->config = $config;
  }

  public function createConnection()
  {
    $databasePath = $this->config['database'] ?? null;

    if (!$databasePath) {
      throw new InvalidArgumentException("SQLite database path not specified");
    }

    try {
      $pdo = new PDO("sqlite:$databasePath");
      // Additional PDO configurations or settings can be added here if needed
      return $pdo;
    } catch (PDOException $e) {
      throw new RuntimeException("Failed to connect to SQLite database: " . $e->getMessage());
    }
  }
}
