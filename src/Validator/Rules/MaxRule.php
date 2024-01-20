<?php

namespace Stag\Validator\Rules;

class MaxRule implements RuleInterface
{

  public function validate($field, $value, $parameters = [])
  {
    $maxValue = $parameters[0];

    if (strlen($value) > $maxValue) {
      return "The $field must not exceed $maxValue characters.";
    }

    return null;
  }
}
