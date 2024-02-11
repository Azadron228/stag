<?php

namespace Stag\Auth;

class Auth implements AuthInterface
{

  public function __construct()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  public static function login($user)
  {
    $_SESSION['authenticated'] = true;
    $_SESSION['user_id'] = $user['id'];

    $token = bin2hex(random_bytes(32));
    setcookie("AuthToken", $token, time() + 60 * 60 * 24 * 7, "/", "", true, true);
    $_SESSION['AuthToken'] = $token;
  }

  public static function attempt(array $user, string $password)
  {
    if (password_verify($password, $user['password'])) {
      self::login($user);

      return true;
    }
    return false;
  }

  public static function isAuthenticated()
  {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true &&
      isset($_COOKIE['AuthToken']) && $_COOKIE['AuthToken'] === $_SESSION['AuthToken'];
  }

  public static function logout()
  {
    unset($_SESSION['authenticated']);
    unset($_SESSION['user_id']);
    session_destroy();
  }

  public static function id()
  {
    return $_SESSION['user_id'];
  }

  public static function bcrypt(string $password): string
  {
    return password_hash($password, PASSWORD_DEFAULT);
  }
}
