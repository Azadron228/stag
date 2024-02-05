<?php

use Nyholm\Psr7\Response;

if (!function_exists('response')) {
  function response()
  {
    return new Response();
  }
}
