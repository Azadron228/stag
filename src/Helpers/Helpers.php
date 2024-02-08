<?php

use Nyholm\Psr7\Response;

if (!function_exists('response')) {
  function response()
  {
    return new Response();
  }
}


if (!function_exists('request')) {
  function request()
  {
    $psr17Factory = new Psr17Factory();
    $creator = new ServerRequestCreator(
      $psr17Factory, // ServerRequestFactory
      $psr17Factory, // UriFactory
      $psr17Factory, // UploadedFileFactory
      $psr17Factory  // StreamFactory
    );
    return $creator->fromGlobals();
  }
}
