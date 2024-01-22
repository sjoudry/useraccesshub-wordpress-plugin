<?php

namespace UserAccessHub\Endpoint;

/**
 * Base Endpoint class.
 *
 * @since 1.0
 */
class Base {

  /**
   * Error response if the body is malformed.
   *
   * @param string[] $errors
   *   The list of errors to add to the response.
   *
   * @since 1.0
   */
  protected function errorResponseBody(array $errors = []) {
    $message = [
      'message' => 'The body of the request is required.',
      'errors' => $errors,
    ];
    $this->response($message, 400);
  }

  /**
   * Handshake error response.
   *
   * @since 1.0
   */
  protected function errorResponseHandshake() {
    $message = [
      'error' => 1,
      'message' => 'Handshake is forbidden.',
    ];
    $this->response($message, 200);
  }

  /**
   * API Key error response.
   *
   * @since 1.0
   */
  protected function errorResponseKey() {
    $message = ['message' => 'The API key is invalid.'];
    $this->response($message, 401);
  }

  /**
   * Login error response.
   *
   * @since 1.0
   */
  protected function errorResponseLogin() {
    $message = ['message' => 'Login functionality using this method is disabled.'];
    $this->response($message, 403);
  }

  /**
   * Message error response.
   *
   * @since 1.0
   */
  protected function errorResponseMessage() {
    $message = ['message' => 'The message POST variable is required and needs to be base 64 encoded.'];
    $this->response($message, 405);
  }

  /**
   * HTTP method error response.
   *
   * @since 1.0
   */
  protected function errorResponseMethod() {
    $message = ['message' => 'The method is not allowed.'];
    $this->response($message, 405);
  }

  /**
   * Signature error response.
   *
   * @since 1.0
   */
  protected function errorResponseSignature() {
    $message = ['message' => 'The signature is invalid.'];
    $this->response($message, 401);
  }

  /**
   * Time error response.
   *
   * @since 1.0
   */
  protected function errorResponseTimes() {
    $message = ['message' => 'The login request has expired.'];
    $this->response($message, 401);
  }

  /**
   * Get roles.
   *
   * @param array $managed_roles
   *   The roles that are configured for user access hub.
   *
   * @return array
   *   A list of role names, keyed by role id.
   *
   * @since 1.0
   */
  protected function getRoles(array $managed_roles) {
    global $wp_roles;

    // Return the roles of the site.
    $roles = [];
    foreach ($wp_roles->roles as $role_id => $wp_role) {
      if (in_array($role_id, $managed_roles)) {
        $roles[$role_id] = $wp_role['name'];
      }
    }

    return $roles;
  }

  /**
   * Response.
   *
   * @param array $body
   *   The body of the response.
   * @param int $http_code
   *   The response HTTP status code.
   *
   * @since 1.0
   */
  protected function response(array $body, int $http_code) {
    switch ($http_code) {
      case 200:
        header('Status: 200 Accepted');
        header('HTTP/1.1 200 Accepted');
        break;
      case 400:
        header('Status: 400 Bad Request');
        header('HTTP/1.1 400 Bad Request');
        break;
      case 401:
        header('Status: 401 Unauthorized');
        header('HTTP/1.1 401 Unauthorized');
        break;
      case 403:
        header('Status: 403 Forbidden');
        header('HTTP/1.1 403 Forbidden');
        break;
      case 405:
        header('Status: 405 Method Not Allowed');
        header('HTTP/1.1 405 Method Not Allowed');
        break;
    }

    if (count($body)) {
      header('Content-Type: application/json');
      $body = json_encode($body);

      // Compress only if client request allows gzip.
      if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
        $body = gzencode($body, 9, FORCE_GZIP);
        header('Content-Encoding: gzip');
      }

      print $body;
    }

    exit();
  }

  /**
   * Validate API key.
   *
   * @param string $api_key
   *   The site API key.
   *
   * @return bool
   *   TRUE if the apikey header value matches key and FALSE otherwise.
   *
   * @since 1.0
   */
  protected function validateApiKey(string $api_key) {
    if (isset($_SERVER['HTTP_APIKEY']) && $_SERVER['HTTP_APIKEY'] == $api_key) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Validate request body.
   *
   * @param string $body
   *   The body of the request.
   * @param array $properties
   *   The properties required for the body.
   *
   * @return string[]
   *   The array of errors.
   *
   * @since 1.0
   */
  protected function validateBody(string $body = NULL, array $properties = []) {
    $errors = [];

    if (empty($body)) {
      $errors[] = 'Body is missing and is required.';
    }
    else {
      $json_body = @json_decode($body);
      if (json_last_error() != JSON_ERROR_NONE) {
        $errors[] = 'Invalid JSON: ' . json_last_error_msg();
      }

      foreach ($properties as $property) {
        if (!isset($json_body->{$property})) {
          $errors[] = "The '" . $property . "' property is missing";
        }
      }
    }

    return $errors;
  }

  /**
   * Validate HTTP method.
   *
   * @param array $methods
   *   The accepted methods.
   *
   * @return bool
   *   TRUE if the http method matches the method's passed in and FALSE
   *   otherwise.
   *
   * @since 1.0
   */
  protected function validateMethod(array $methods) {
    if (in_array($_SERVER['REQUEST_METHOD'], $methods)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Validate Signature.
   *
   * @param string $data
   *   The data to validate.
   * @param string $signature
   *   The signature to validate.
   * @param string $public_key
   *   The site's public key.
   *
   * @return bool
   *   TRUE if the signature header value matches key and FALSE otherwise.
   *
   * @since 1.0
   */
  protected function validateSignature(string $data, string $signature, string $public_key) {
    if (openssl_verify($data, $signature, $public_key, OPENSSL_ALGO_SHA384) == 1) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Validate times.
   *
   * @param object $data
   *   The message data.
   *
   * @return string[]
   *   The array of errors.
   *
   * @since 1.0
   */
  protected function validateTimes(object $data) {

    // These dates will be passed in ISO 8601 date format.
    $created = strtotime($data->created);
    $expiry = strtotime($data->expiry);

    // The request from the User Access Hub must have been created before now
    // and must expire after now.
    $now = time();

    if ($created <= $now && $expiry >= $now) {
      return TRUE;
    }

    return FALSE;
  }

}