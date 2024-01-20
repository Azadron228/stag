<?php

namespace Stag\Auth;

interface AuthInterface
{
  public static function bcrypt(string $password): string;
}
