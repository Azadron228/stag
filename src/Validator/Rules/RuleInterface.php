<?php

namespace Stag\Validator\Rules;

interface RuleInterface
{
  public function validate($field, $value, $parameters = []);
}
