<?php

/**
 * Error response if the body is malformed.
 *
 * @param string[] $errors
 *   The list of errors to add to the response.
 */
function _useraccesshub_error_response_body(array $errors = []) {
  $message = [
    'message' => 'The body of the request is required.',
    'errors' => $errors,
  ];
  _useraccesshub_response($message, 400);
}

/**
 * Handshake error response.
 */
function _useraccesshub_error_response_handshake() {
  $message = [
    'error' => 1,
    'message' => 'Handshake is forbidden.',
  ];
  _useraccesshub_response($message, 200);
}

/**
 * API Key error response.
 */
function _useraccesshub_error_response_key() {
  $message = ['message' => 'The API key is invalid.'];
  _useraccesshub_response($message, 401);
}

/**
 * Login error response.
 */
function _useraccesshub_error_response_login() {
  $message = ['message' => 'Login functionality using this method is disabled.'];
  _useraccesshub_response($message, 403);
}

/**
 * Message error response.
 */
function _useraccesshub_error_response_message() {
  $message = ['message' => 'The message POST variable is required and needs to be base 64 encoded.'];
  _useraccesshub_response($message, 405);
}

/**
 * HTTP method error response.
 */
function _useraccesshub_error_response_method() {
  $message = ['message' => 'The method is not allowed.'];
  _useraccesshub_response($message, 405);
}

/**
 * Signature error response.
 */
function _useraccesshub_error_response_signature() {
  $message = ['message' => 'The signature is invalid.'];
  _useraccesshub_response($message, 401);
}

/**
 * Time error response.
 */
function _useraccesshub_error_response_times() {
  $message = ['message' => 'The login request has expired.'];
  _useraccesshub_response($message, 401);
}

/**
 * Get roles.
 *
 * @param array $configured_roles
 *   The roles that are configured for user access hub.
 *
 * @return array
 *   A list of role names, keyed by role id.
 */
function _useraccesshub_get_roles(array $configured_roles) {
  global $wp_roles;

  // Return the roles of the site.
  $roles = [];
  foreach ($wp_roles->roles as $role_id => $wp_role) {
    if (in_array($role_id, $configured_roles)) {
      $roles[$role_id] = $wp_role['name'];
    }
  }

  return $roles;
}

/**
 * Validate API key.
 *
 * @param string $api_key
 *   The site API key.
 *
 * @return bool
 *   TRUE if the apikey header value matches key and FALSE otherwise.
 */
function _useraccesshub_validate_api_key(string $api_key) {
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
 */
function _useraccesshub_validate_body(string $body = NULL, array $properties = []) {
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
 */
function _useraccesshub_validate_method(array $methods) {
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
 */
function _useraccesshub_validate_signature(string $data, string $signature, string $public_key) {
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
 */
function _useraccesshub_validate_times(object $data) {

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
