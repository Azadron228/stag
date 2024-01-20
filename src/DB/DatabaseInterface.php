<?php

namespace Stag\DB;

interface DatabaseInterface
{

  public function executeQuery(string $sql, array $params = [], $types = []);
}
