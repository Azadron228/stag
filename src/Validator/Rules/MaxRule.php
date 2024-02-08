<?php

namespace Stag\Validator\Rules;

class MaxRule implements RuleInterface
{

  public function validate($field, $value, $parameters = [])
  {
    if (is_string($value)) {
      if (!empty($parameters) && isset($parameters[0])) {
        $minValue = $parameters[0];

        if (strlen($value) > $minValue) {
          return "The $field must not exceed $minValue characters.";
        }
      } else {
        return "The minimum value parameter is missing for $field validation.";
      }
    } else {
      return "The $field must be a valid string.";
    }

    return null;
  }
}
