<?php

namespace Stag\Validator\Rules;

use InvalidArgumentException;
use Stag\DB\Database;

class UniqueRule implements RuleInterface
{
  protected Database $database;

  public function __construct(Database $database)
  {
    $this->database = $database;
  }

  public function validate($field, $value, $parameters = [])
  {
    $tableName = $parameters[0] ?? null;
    $columnName = $field ?? null;

    if (!$tableName || !$columnName) {
      throw new InvalidArgumentException("Table name and column name are required parameters for the unique rule.");
    }

    $params = [':value' => $value];

    $query = $this->database->executeQuery("SELECT COUNT(*) FROM $tableName WHERE $columnName = :value", $params);

    $count = $query->fetchColumn();

    if ($count > 0) {
      return "The $field has already been taken.";
    }

    return null;
  }
}
