<?php

namespace Stag\Validator\Rules;

class MinRule
{
  public function validate($field, $value, $parameters)
  {
    $minValue = $parameters[0];

    if (strlen($value) < $minValue) {
      return "The $field must not exceed $minValue characters.";
    }

    return null;
  }
}
