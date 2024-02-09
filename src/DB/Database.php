<?php

namespace Stag\DB;

use Stag\DB\DatabaseInterface;
use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

class Database implements DatabaseInterface
{
  protected $connection;

  public function __construct(array $config)
  {
    $driverManager = new DriverManager($config);
    $this->connection = $driverManager->createConnection();
  }

  public function getConnection()
  {
    return $this->connection;
  }

  public function executeQuery(string $sql, array $params = [], $types = [])
  {
    try {
      $statement = $this->getConnection()->prepare($sql);

      foreach ($params as $key => $value) {
        $type = $types[$key] ?? PDO::PARAM_STR;
        $statement->bindValue($key, $value, $type);
      }

      $statement->execute();

      return $statement;
    } catch (PDOException $e) {
      throw new RuntimeException("Error executing query: " . $e->getMessage());
    }
  }
}
