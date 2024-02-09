<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7Server\ServerRequestCreator;

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


if (!function_exists('json_response')) {
  function json_response(array $data, int $statusCode = 200): Response
  {
    $psr17Factory = new Psr17Factory();
    $responseBody = $psr17Factory->createStream(json_encode($data));

    return $psr17Factory
      ->createResponse($statusCode)
      ->withBody($responseBody)
      ->withHeader('Content-Type', 'application/json');
  }
}
